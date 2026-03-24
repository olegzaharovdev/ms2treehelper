# ms2TreeHelper

`ms2TreeHelper` — дополнительный компонент для **MODX Revolution** и **miniShop2**, который добавляет массовые действия в дерево категорий в окне создания и редактирования опций товаров.
Версия 1.1.0
## Возможности

- массовое назначение опции на все категории
- назначение опции на все дочерние категории выбранной категории
- снятие опции со всех категорий
- встроенные компактные кнопки прямо в toolbar дерева miniShop2
- всплывающие подсказки в стиле ExtJS manager

## Что улучшено в 1.1.0

- replaced continuous polling with `MutationObserver`
- added explicit miniShop2 context detection before initialization
- improved tooltip lifecycle and body cleanup on hide
- made PNG icons larger and more contrast
- fixed version and date metadata in docs and setup options
- added lightweight debug logging via `window.ms2TreeHelperDebug = true`

## Преимущества

- не требует правок `core` MODX
- не требует правок `core` miniShop2
- не меняет структуру базы данных
- не вмешивается в процессоры miniShop2
- подключается как отдельный extra
- ускоряет работу администратора и контент-менеджера

## Совместимость

- **MODX Revolution 2.8.x / 3.x**
- **miniShop2 3.x**

## Автор

**Oleg Zakharov with OpenAi Generated ChatGPT.com**

## Лицензия

**GNU GPL v2**
