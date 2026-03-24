# ms2TreeHelper

`ms2TreeHelper` — дополнительный компонент для **MODX Revolution** и **miniShop2**, который добавляет массовые действия в дерево категорий в окне создания и редактирования опций товаров.

## Возможности

- добавить опцию на все категории
- добавить опцию ко всем дочерним категориям выбранной категории
- снять опцию со всех категорий

## Что улучшено в 1.1.0

- переход с `setInterval` на `MutationObserver`
- проверка miniShop2-контекста до запуска логики
- ExtJS tooltip с очисткой содержимого при скрытии
- более контрастные иконки
- исправлены версия и даты в документации
- добавлен debug-флаг `window.ms2TreeHelperDebug = true`

## Структура пакета

- `_build/` — сборка transport package
- `core/components/ms2treehelper/` — plugin + docs
- `assets/components/ms2treehelper/` — manager JS + icons

## Быстрый старт

1. Положить папку `ms2treehelper-src-1.1.0-pl` в корень сайта MODX.
2. Открыть `/ms2treehelper-src-1.1.0-pl/_build/build.transport.php`.
3. Забрать zip из `core/packages/`.
4. Установить через `Extras -> Installer`.

## Автор

**Oleg Zakharov with OpenAi Generated ChatGPT.com**

## Лицензия

**GNU GPL v2**
