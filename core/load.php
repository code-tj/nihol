<?php
// для ускорения автоладинга используем следующий способ
///set_include_path(get_include_path().PATH_SEPARATOR.'./app/lib/'); // в данном случае приоритет для классов приложения
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();
// ниже можно определить наиболее часто используемые классы (микрофреймворка)
