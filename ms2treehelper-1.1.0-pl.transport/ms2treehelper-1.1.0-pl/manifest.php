<?php return array (
  'manifest-version' => '1.1',
  'manifest-attributes' => 
  array (
    'license' => 'GNU GENERAL PUBLIC LICENSE
Version 2, June 1991

Copyright (C) 1989, 1991 Free Software Foundation, Inc.
59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Everyone is permitted to copy and distribute verbatim copies
of this license document, but changing it is not allowed.

Preamble

The licenses for most software are designed to take away your freedom to share and change it. By contrast, the GNU General Public License is intended to guarantee your freedom to share and change free software--to make sure the software is free for all its users. This General Public License applies to most of the Free Software Foundation\'s software and to any other program whose authors commit to using it. (Some other Free Software Foundation software is covered by the GNU Library General Public License instead.) You can apply it to your programs, too.

When we speak of free software, we are referring to freedom, not price. Our General Public Licenses are designed to make sure that you have the freedom to distribute copies of free software (and charge for this service if you wish), that you receive source code or can get it if you want it, that you can change the software or use pieces of it in new free programs; and that you know you can do these things.

To protect your rights, we need to make restrictions that forbid anyone to deny you these rights or to ask you to surrender the rights. These restrictions translate to certain responsibilities for you if you distribute copies of the software, or if you modify it.

For example, if you distribute copies of such a program, whether gratis or for a fee, you must give the recipients all the rights that you have. You must make sure that they, too, receive or can get the source code. And you must show them these terms so they know their rights.

We protect your rights with two steps: (1) copyright the software, and (2) offer you this license which gives you legal permission to copy, distribute and/or modify the software.

Also, for each author\'s protection and ours, we want to make certain that everyone understands that there is no warranty for this free software. If the software is modified by someone else and passed on, we want its recipients to know that what they have is not the original, so that any problems introduced by others will not reflect on the original authors\' reputations.

Finally, any free program is threatened constantly by software patents. We wish to avoid the danger that redistributors of a free program will individually obtain patent licenses, in effect making the program proprietary. To prevent this, we have made it clear that any patent must be licensed for everyone\'s free use or not licensed at all.

TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

0. This License applies to any program or other work which contains a notice placed by the copyright holder saying it may be distributed under the terms of this General Public License.

1. You may copy and distribute verbatim copies of the Program\'s source code as you receive it, in any medium, provided that you conspicuously and appropriately publish on each copy an appropriate copyright notice and disclaimer of warranty; keep intact all the notices that refer to this License and to the absence of any warranty; and give any other recipients of the Program a copy of this License along with the Program.

2. You may modify your copy or copies of the Program or any portion of it, thus forming a work based on the Program, and copy and distribute such modifications or work under the terms of Section 1 above, provided that you also meet all of these conditions: a) You must cause the modified files to carry prominent notices stating that you changed the files and the date of any change. b) You must cause any work that you distribute or publish, that in whole or in part contains or is derived from the Program or any part thereof, to be licensed as a whole at no charge to all third parties under the terms of this License.

3. You may copy and distribute the Program (or a work based on it) in object code or executable form under the terms of Sections 1 and 2 above provided that you also do one of the following: accompany it with the complete corresponding machine-readable source code; or accompany it with a written offer, valid for at least three years, to give any third party a complete machine-readable copy of the corresponding source code.

4. You may not copy, modify, sublicense, or distribute the Program except as expressly provided under this License.

5. By modifying or distributing the Program (or any work based on the Program), you indicate your acceptance of this License to do so, and all its terms and conditions for copying, distributing or modifying the Program or works based on it.

6. Each time you redistribute the Program (or any work based on the Program), the recipient automatically receives a license from the original licensor to copy, distribute or modify the Program subject to these terms and conditions.

7. If conditions are imposed on you that contradict the conditions of this License, they do not excuse you from the conditions of this License.

8. If the distribution and/or use of the Program is restricted in certain countries either by patents or by copyrighted interfaces, the original copyright holder may add an explicit geographical distribution limitation excluding those countries.

9. The Free Software Foundation may publish revised and/or new versions of the General Public License from time to time.

10. If you wish to incorporate parts of the Program into other free programs whose distribution conditions are different, write to the author to ask for permission.

NO WARRANTY

11. BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY FOR THE PROGRAM.

12. IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER BE LIABLE TO YOU FOR DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE PROGRAM.

END OF TERMS AND CONDITIONS
',
    'readme' => '--------------------
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
',
    'changelog' => 'ms2TreeHelper 1.1.0-pl [2026-03-25]
===================================
- Changed: replaced continuous setInterval scanning with MutationObserver-based detection.
- Added: explicit miniShop2 context check before JS initialization.
- Added: safer tooltip lifecycle with tooltip body cleanup on hide/blur.
- Improved: toolbar icons are larger and more contrast.
- Improved: basic debug logging via window.ms2TreeHelperDebug.
- Fixed: setup.options.php version updated to 1.1.0-pl.
- Fixed: package docs and metadata updated to current version and date.

ms2TreeHelper 1.0.9-pl [2026-03-24]
===================================
- Added: native ExtJS tooltips for custom toolbar buttons in the miniShop2 option tree.
- Added: tooltip body is filled on hover/focus and cleared on hide for cleaner manager behavior.
- Improved: toolbar PNG icons are now larger, sharper and more contrast for MODX manager themes.
- Fixed: custom buttons now use direct PNG background-image URLs from component assets.

ms2TreeHelper 1.0.5-pl [2026-03-24]
===================================
- Added: compact icon buttons in the native tree toolbar style.
- Changed: bulk actions are now rendered inside the existing toolbar next to expand, collapse and refresh.
- Improved: fallback rendering preserved for non-standard manager layouts.

ms2TreeHelper 1.0.4-pl [2026-03-24]
===================================
- Fixed: removed stale old file resolver paths from transport vehicle.
- Fixed: plugin now loads manager JS without strict namespace dependency.
- Fixed: JS now targets real miniShop2 option tree ids.
- Added: DOM fallback for checkbox collection and hidden categories input sync.

ms2TreeHelper 1.0.3-pl [2026-03-24]
===================================
- Fixed: corrected package version in build.transport.php to avoid manifest path mismatch during install.
- Added: setup-options.php to show component description during install.
- Updated: readme, license and changelog package attributes.

ms2TreeHelper 1.0.2-pl [2026-03-24]
===================================
- Fixed: package builder fatal error in transport.plugins.php.
- Improved: build.config.php path/url autodetection for non-standard MODX layouts.

ms2TreeHelper 1.0.0-pl [2026-03-24]
===================================
- Initial release.
',
    'setup-options' => 'ms2treehelper-1.1.0-pl/setup-options.php',
  ),
  'manifest-vehicles' => 
  array (
    0 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modNamespace',
      'guid' => '108de816aa40869564b89a0f20270df1',
      'native_key' => 'ms2treehelper',
      'filename' => 'modNamespace/a3d861e1fb84325e68b93bbfba36a50b.vehicle',
      'namespace' => 'ms2treehelper',
    ),
    1 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modCategory',
      'guid' => 'bdaffa0ff9ccfc189918df9ecf3edcda',
      'native_key' => NULL,
      'filename' => 'modCategory/6d8c8e741af8d0921178c9ff809aae1a.vehicle',
      'namespace' => 'ms2treehelper',
    ),
  ),
);