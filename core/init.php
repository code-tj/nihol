<?php
// для ускорения автоладинга используем следующий упрощенный способ
set_include_path(get_include_path().PATH_SEPARATOR.'./app/lib/'); // в данном случае приоритет классов уровня приложения
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();

// ниже можно определить наиболее часто используемые классы уровня ядра приложения (микрофреймворка)