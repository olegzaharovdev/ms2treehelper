Ext.onReady(function () {
    var TEXT = {
        checkAll: 'Добавить на все категории',
        checkChildren: 'Добавить ко всем дочерним категориям',
        uncheckAll: 'Снять со всех категорий',
        noSelected: 'Сначала выделите элемент в дереве',
        title: 'ms2TreeHelper'
    };

    var DEBUG = !!window.ms2TreeHelperDebug;
    var TOOLTIP = null;
    var OBSERVER = null;
    var SCAN_TIMER = null;
    var FALLBACK_TIMER = null;
    var FALLBACK_TICKS = 0;

    function log() {
        if (!DEBUG || !window.console || !console.log) return;
        var args = Array.prototype.slice.call(arguments);
        args.unshift('[ms2TreeHelper]');
        console.log.apply(console, args);
    }

    function warn() {
        if (!window.console || !console.warn) return;
        var args = Array.prototype.slice.call(arguments);
        args.unshift('[ms2TreeHelper]');
        console.warn.apply(console, args);
    }

    function isMiniShop2Context() {
        try {
            var href = String(window.location.href || '');
            if (href.indexOf('namespace=minishop2') !== -1) {
                return true;
            }

            if (document.getElementById('minishop2-window-option-create-option-categories')
                || document.getElementById('minishop2-window-option-update-option-categories')) {
                return true;
            }

            if (document.body && document.body.innerHTML.indexOf('minishop2-window-option-') !== -1) {
                return true;
            }
        } catch (e) {
            warn('Context detection error:', e);
        }

        return false;
    }

    function getAssetsBaseUrl() {
        if (window.ms2TreeHelperAssetsUrl) {
            return window.ms2TreeHelperAssetsUrl;
        }

        var scripts = document.getElementsByTagName('script');
        for (var i = 0; i < scripts.length; i++) {
            var src = scripts[i].getAttribute('src') || '';
            if (src.indexOf('components/ms2treehelper/js/mgr/ms2treehelper.js') !== -1) {
                return src.replace(/js\/mgr\/ms2treehelper\.js(?:\?.*)?$/, '');
            }
        }

        return '/assets/components/ms2treehelper/';
    }

    var ASSETS = getAssetsBaseUrl();
    var ICONS = {
        checkAll: ASSETS + 'img/checkall.png',
        checkChildren: ASSETS + 'img/checkchildren.png',
        uncheckAll: ASSETS + 'img/uncheckall.png'
    };

    function ensureStyles() {
        if (document.getElementById('ms2treehelper-styles')) {
            return;
        }

        var css = ''
            + '.ms2treehelper-toolbar-fallback{margin:6px 0 8px 0;display:flex;gap:4px;flex-wrap:wrap;}'
            + '.ms2treehelper-toolbar-fallback .x-btn button{padding:0 6px;}';

        var style = document.createElement('style');
        style.type = 'text/css';
        style.id = 'ms2treehelper-styles';
        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        document.getElementsByTagName('head')[0].appendChild(style);
    }

    function ensureTooltip() {
        if (TOOLTIP || typeof Ext === 'undefined' || !Ext.ToolTip) {
            return TOOLTIP;
        }

        try {
            if (Ext.QuickTips && Ext.QuickTips.init) {
                Ext.QuickTips.init();
            }
            if (Ext.QuickTips && Ext.QuickTips.getQuickTip) {
                Ext.apply(Ext.QuickTips.getQuickTip(), {
                    dismissDelay: 0,
                    interceptTitles: true
                });
            }

            TOOLTIP = new Ext.ToolTip({
                target: Ext.getBody(),
                html: '',
                dismissDelay: 0,
                showDelay: 120,
                hideDelay: 0,
                autoHide: true,
                trackMouse: false
            });

            TOOLTIP.on('hide', function (tip) {
                setTooltipHtml(tip, '');
            });
        } catch (e) {
            warn('Tooltip init failed:', e);
        }

        return TOOLTIP;
    }

    function setTooltipHtml(tip, html) {
        if (!tip) {
            return;
        }

        html = html || '';

        try {
            if (tip.body && tip.body.update) {
                tip.body.update(html);
                return;
            }

            if (tip.el) {
                var body = tip.el.child('.x-tip-body');
                if (body && body.update) {
                    body.update(html);
                    return;
                }
            }

            tip.html = html;
        } catch (e) {
            warn('Tooltip update failed:', e);
        }
    }

    function bindTooltip(buttonEl, tooltip) {
        if (!buttonEl || buttonEl._ms2treehelperTipBound) {
            return;
        }

        buttonEl._ms2treehelperTipBound = true;

        buttonEl.setAttribute('title', tooltip);
        buttonEl.setAttribute('data-qtip', tooltip);

        var show = function () {
            var tip = ensureTooltip();
            if (!tip || !buttonEl.offsetParent) {
                return;
            }

            setTooltipHtml(tip, tooltip);

            try {
                tip.target = buttonEl;
                tip.showBy(buttonEl);
            } catch (e) {
                warn('Tooltip show failed:', e);
            }
        };

        var hide = function () {
            if (!TOOLTIP) {
                return;
            }

            try {
                TOOLTIP.hide();
                setTooltipHtml(TOOLTIP, '');
            } catch (e) {
                warn('Tooltip hide failed:', e);
            }
        };

        buttonEl.addEventListener('mouseenter', show);
        buttonEl.addEventListener('mouseleave', hide);
        buttonEl.addEventListener('focus', show);
        buttonEl.addEventListener('blur', hide);
    }

    function eachNodeAsync(node, includeSelf, iterator, done) {
        if (!node) {
            if (done) done();
            return;
        }

        var process = function () {
            if (includeSelf) {
                iterator(node);
            }

            var children = node.childNodes || [];
            var index = 0;

            var next = function () {
                if (index >= children.length) {
                    if (done) done();
                    return;
                }

                eachNodeAsync(children[index], true, iterator, function () {
                    index++;
                    next();
                });
            };

            next();
        };

        var loaded = true;
        if (typeof node.isLoaded === 'function') {
            loaded = node.isLoaded();
        }

        if (node.leaf || loaded) {
            process();
        } else {
            node.expand(false, false, function () {
                process();
            });
        }
    }

    function setNodeChecked(tree, node, checked) {
        if (!node) {
            return;
        }

        node.attributes = node.attributes || {};
        node.attributes.checked = checked;

        var ui = (typeof node.getUI === 'function') ? node.getUI() : node.ui;
        if (ui && ui.checkbox) {
            ui.checkbox.checked = checked;
        }

        if (tree && tree.fireEvent) {
            tree.fireEvent('checkchange', node, checked);
        }
    }

    function getTreeCmp(panelEl) {
        if (!panelEl || !panelEl.id || typeof Ext === 'undefined' || !Ext.getCmp) {
            return null;
        }

        try {
            return Ext.getCmp(panelEl.id) || null;
        } catch (e) {
            return null;
        }
    }

    function getWindowEl(panelEl) {
        return panelEl ? panelEl.closest('.x-window') : null;
    }

    function getCategoriesInput(panelEl) {
        var win = getWindowEl(panelEl);
        if (!win) {
            return null;
        }

        return win.querySelector('input[name="categories"]');
    }

    function extractNumericId(nodeId) {
        if (!nodeId) {
            return null;
        }

        var match = String(nodeId).match(/(\d+)$/);
        return match ? parseInt(match[1], 10) : null;
    }

    function getNodeIdFromCheckbox(checkbox) {
        var li = checkbox ? checkbox.closest('li.x-tree-node') : null;
        if (!li) {
            return null;
        }

        var el = li.querySelector('.x-tree-node-el');
        if (!el) {
            return null;
        }

        return el.getAttribute('ext:tree-node-id') || el.getAttribute('data-node-id') || null;
    }

    function updateHiddenCategories(panelEl) {
        var input = getCategoriesInput(panelEl);
        if (!input) {
            return;
        }

        var ids = [];
        var seen = {};

        var checked = panelEl.querySelectorAll('input.x-tree-node-cb:checked');
        for (var i = 0; i < checked.length; i++) {
            var rawId = getNodeIdFromCheckbox(checked[i]);
            var numId = extractNumericId(rawId);
            if (numId !== null && !seen[numId]) {
                seen[numId] = true;
                ids.push(numId);
            }
        }

        input.value = Ext.util.JSON.encode(ids);
    }

    function rememberSelected(panelEl, target) {
        if (!panelEl || !target) {
            return;
        }

        var row = target.closest('.x-tree-node-el');
        if (!row) {
            return;
        }

        panelEl._ms2treehelperSelectedRow = row;
        var nodeId = row.getAttribute('ext:tree-node-id');
        if (nodeId) {
            panelEl._ms2treehelperSelectedNodeId = nodeId;
        }
    }

    function getSelectedDomRow(panelEl) {
        if (!panelEl) {
            return null;
        }

        if (panelEl._ms2treehelperSelectedRow) {
            return panelEl._ms2treehelperSelectedRow;
        }

        var selectedAnchor = panelEl.querySelector('.x-tree-selected');
        if (selectedAnchor) {
            return selectedAnchor.closest('.x-tree-node-el');
        }

        return null;
    }

    function getSelectedExtNode(panelEl) {
        var tree = getTreeCmp(panelEl);
        if (!tree) {
            return null;
        }

        try {
            var sm = tree.getSelectionModel ? tree.getSelectionModel() : null;
            if (sm && sm.getSelectedNode) {
                var selected = sm.getSelectedNode();
                if (selected) {
                    return selected;
                }
            }
        } catch (e) {}

        var nodeId = panelEl._ms2treehelperSelectedNodeId;
        if (nodeId && tree.getNodeById) {
            try {
                var byId = tree.getNodeById(nodeId);
                if (byId) {
                    return byId;
                }
            } catch (e2) {}
        }

        return null;
    }

    function toggleAllByDom(panelEl, checked) {
        var boxes = panelEl.querySelectorAll('input.x-tree-node-cb');
        for (var i = 0; i < boxes.length; i++) {
            boxes[i].checked = checked;
        }
        updateHiddenCategories(panelEl);
    }

    function toggleChildrenByDom(panelEl) {
        var row = getSelectedDomRow(panelEl);
        if (!row) {
            MODx.msg.alert(TEXT.title, TEXT.noSelected);
            return;
        }

        var li = row.closest('li.x-tree-node');
        if (!li) {
            MODx.msg.alert(TEXT.title, TEXT.noSelected);
            return;
        }

        var subtree = li.querySelector('ul.x-tree-node-ct');
        if (!subtree) {
            updateHiddenCategories(panelEl);
            return;
        }

        var boxes = subtree.querySelectorAll('input.x-tree-node-cb');
        for (var i = 0; i < boxes.length; i++) {
            boxes[i].checked = true;
        }

        updateHiddenCategories(panelEl);
    }

    function toggleAllByExt(panelEl, checked) {
        var tree = getTreeCmp(panelEl);
        if (!tree || !tree.getRootNode) {
            toggleAllByDom(panelEl, checked);
            return;
        }

        var root = tree.getRootNode();
        eachNodeAsync(root, false, function (node) {
            setNodeChecked(tree, node, checked);
        }, function () {
            updateHiddenCategories(panelEl);
        });
    }

    function toggleChildrenByExt(panelEl) {
        var tree = getTreeCmp(panelEl);
        var selected = getSelectedExtNode(panelEl);

        if (!tree || !selected) {
            toggleChildrenByDom(panelEl);
            return;
        }

        eachNodeAsync(selected, false, function (node) {
            setNodeChecked(tree, node, true);
        }, function () {
            updateHiddenCategories(panelEl);
        });
    }

    function bindPanelEvents(panelEl) {
        if (!panelEl || panelEl._ms2treehelperEventsBound) {
            return;
        }

        panelEl._ms2treehelperEventsBound = true;

        panelEl.addEventListener('click', function (e) {
            var t = e.target;
            if (!t) {
                return;
            }

            if (t.closest('.x-tree-node-el') || t.closest('.x-tree-node-anchor')) {
                rememberSelected(panelEl, t);
            }

            if (t.matches && t.matches('input.x-tree-node-cb')) {
                window.setTimeout(function () {
                    updateHiddenCategories(panelEl);
                }, 0);
            }
        });
    }

    function applyIconStyle(btn, tooltip, iconUrl) {
        if (!btn || !btn.getEl) {
            return;
        }

        var btnEl = btn.getEl();
        if (!btnEl || !btnEl.dom) {
            return;
        }

        btnEl.dom.setAttribute('title', tooltip);
        btnEl.dom.setAttribute('data-qtip', tooltip);

        var buttonEl = btnEl.child('button.x-btn-text');
        if (!buttonEl || !buttonEl.dom) {
            return;
        }

        var dom = buttonEl.dom;
        dom.setAttribute('title', tooltip);
        dom.innerHTML = '&nbsp;';
        dom.style.backgroundImage = 'url("' + iconUrl + '")';
        dom.style.backgroundRepeat = 'no-repeat';
        dom.style.backgroundPosition = 'center center';
        dom.style.backgroundSize = '20px 20px';
        dom.style.width = '20px';
        dom.style.height = '20px';
        dom.style.minWidth = '20px';
        dom.style.cursor = 'pointer';
        dom.style.opacity = '1';
        dom.style.filter = 'none';

        bindTooltip(dom, tooltip);
    }

    function addIconButton(rowEl, tooltip, iconUrl, handler) {
        var td = document.createElement('td');
        td.className = 'x-toolbar-cell';
        rowEl.appendChild(td);

        var host = document.createElement('span');
        td.appendChild(host);

        var btn = new Ext.Button({
            renderTo: host,
            cls: 'x-btn-icon',
            text: '&nbsp;',
            handler: handler
        });

        applyIconStyle(btn, tooltip, iconUrl);
        return btn;
    }

    function renderButtons(panelEl) {
        if (!panelEl || panelEl._ms2treehelperButtonsRendered) {
            return;
        }

        try {
            ensureStyles();

            var tbar = panelEl.querySelector('.x-panel-tbar');
            var toolbarRow = tbar ? tbar.querySelector('.x-toolbar-left-row') : null;

            if (toolbarRow) {
                addIconButton(toolbarRow, TEXT.checkAll, ICONS.checkAll, function () {
                    toggleAllByExt(panelEl, true);
                });

                addIconButton(toolbarRow, TEXT.checkChildren, ICONS.checkChildren, function () {
                    toggleChildrenByExt(panelEl);
                });

                addIconButton(toolbarRow, TEXT.uncheckAll, ICONS.uncheckAll, function () {
                    toggleAllByExt(panelEl, false);
                });
            } else {
                var body = panelEl.querySelector('.x-panel-body');
                if (!body) {
                    return;
                }

                var host = document.createElement('div');
                host.className = 'ms2treehelper-toolbar-fallback';
                panelEl.insertBefore(host, panelEl.firstChild);

                new Ext.Button({
                    renderTo: host,
                    text: TEXT.checkAll,
                    handler: function () {
                        toggleAllByExt(panelEl, true);
                    }
                });

                new Ext.Button({
                    renderTo: host,
                    text: TEXT.checkChildren,
                    handler: function () {
                        toggleChildrenByExt(panelEl);
                    }
                });

                new Ext.Button({
                    renderTo: host,
                    text: TEXT.uncheckAll,
                    handler: function () {
                        toggleAllByExt(panelEl, false);
                    }
                });
            }

            panelEl._ms2treehelperButtonsRendered = true;
            bindPanelEvents(panelEl);
            updateHiddenCategories(panelEl);
            log('Buttons rendered for', panelEl.id);
        } catch (e) {
            warn('Render failed:', e);
        }
    }

    function scan() {
        if (!isMiniShop2Context()) {
            return;
        }

        var panels = document.querySelectorAll('[id^="minishop2-window-option-"][id$="-option-categories"]');
        for (var i = 0; i < panels.length; i++) {
            renderButtons(panels[i]);
        }
    }

    function scheduleScan() {
        if (SCAN_TIMER) {
            clearTimeout(SCAN_TIMER);
        }
        SCAN_TIMER = setTimeout(scan, 60);
    }

    function startObserver() {
        if (!window.MutationObserver || OBSERVER || !document.body) {
            return false;
        }

        OBSERVER = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var m = mutations[i];
                if (m.addedNodes && m.addedNodes.length) {
                    scheduleScan();
                    return;
                }
            }
        });

        OBSERVER.observe(document.body, {
            childList: true,
            subtree: true
        });

        log('MutationObserver started');
        return true;
    }

    function startFallback() {
        if (FALLBACK_TIMER) {
            return;
        }

        FALLBACK_TIMER = setInterval(function () {
            FALLBACK_TICKS++;
            scan();
            if (FALLBACK_TICKS >= 10) {
                clearInterval(FALLBACK_TIMER);
                FALLBACK_TIMER = null;
            }
        }, 1000);
    }

    if (!isMiniShop2Context()) {
        return;
    }

    scan();
    if (!startObserver()) {
        startFallback();
    }

    document.addEventListener('click', scheduleScan, true);
});
