--------------------
Extra: ms2TreeHelper
--------------------
Version: 1.1.0-pl
Since: March 25th, 2026
Author: Oleg Zakharov with OpenAi Generated ChatGPT.com
License: GNU GPLv2 (or later at your option)
Last updated: 2026-03-25

Описание
--------
ms2TreeHelper добавляет в окно создания и редактирования опции miniShop2
дополнительные кнопки массовой отметки категорий прямо в toolbar дерева.

Действия:
- Добавить на все категории
- Добавить ко всем дочерним категориям выделенного элемента
- Снять со всех категорий

Компонент не изменяет core MODX и core miniShop2.
Подключение выполняется через plugin на событие OnManagerPageBeforeRender.

Что входит в пакет
------------------
- Namespace: ms2treehelper
- Plugin: ms2TreeHelper
- Manager JS: assets/components/ms2treehelper/js/mgr/ms2treehelper.js
- PHP resolver: создаёт namespace и привязку plugin к событию OnManagerPageBeforeRender
- Документация: readme, changelog, license, setup-options

Совместимость
-------------
- MODX Revolution 2.8.x / 3.x
- miniShop2 3.x
- Менеджер MODX на ExtJS

Что улучшено в 1.1.0
--------------------
- Основной polling заменён на MutationObserver.
- Добавлена явная проверка miniShop2-контекста до инициализации.
- Обновлены setup-options, README и changelog под версию 1.1.0-pl.
- Иконки стали крупнее и контрастнее.
- Подсказки реализованы через ExtJS ToolTip с очисткой x-tip-body при hide.
- Добавлен debug-флаг: window.ms2TreeHelperDebug = true

Установка
---------
1. Скопируйте transport package в core/packages/
2. Установите пакет через Extras -> Installer
3. Очистите кэш MODX
4. Откройте окно создания или редактирования опции miniShop2
5. В верхней панели дерева категорий появятся маленькие кнопки-иконки

Примечание
----------
Если требуется ручная диагностика, можно включить логирование в консоль браузера:
window.ms2TreeHelperDebug = true
