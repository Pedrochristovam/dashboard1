<?php
declare(strict_types=1);

$appTimezone = getenv('APP_TIMEZONE');
if (is_string($appTimezone) && $appTimezone !== '') {
    date_default_timezone_set($appTimezone);
} else {
    date_default_timezone_set('America/Sao_Paulo');
}

require_once APP_ROOT . '/src/Shared/Helpers.php';
require_once APP_ROOT . '/src/Shared/Session.php';
require_once APP_ROOT . '/src/Shared/Http.php';
require_once APP_ROOT . '/src/Shared/Uploads.php';
require_once APP_ROOT . '/src/Shared/LinkPreview.php';
require_once APP_ROOT . '/src/Shared/View.php';

require_once APP_ROOT . '/src/Auth/Repository.php';
require_once APP_ROOT . '/src/Auth/Actions.php';
require_once APP_ROOT . '/src/Auth/State.php';

require_once APP_ROOT . '/src/Analytics/Repository.php';
require_once APP_ROOT . '/src/Analytics/Actions.php';
require_once APP_ROOT . '/src/Analytics/State.php';

require_once APP_ROOT . '/src/Posts/Repository.php';
require_once APP_ROOT . '/src/Posts/Presenter.php';
require_once APP_ROOT . '/src/Posts/Actions.php';
require_once APP_ROOT . '/src/Posts/State.php';

require_once APP_ROOT . '/src/Innovation/Repository.php';
require_once APP_ROOT . '/src/Innovation/Presenter.php';
require_once APP_ROOT . '/src/Innovation/Actions.php';
require_once APP_ROOT . '/src/Innovation/State.php';

require_once APP_ROOT . '/src/Automation/Repository.php';
require_once APP_ROOT . '/src/Automation/Presenter.php';
require_once APP_ROOT . '/src/Automation/Actions.php';
require_once APP_ROOT . '/src/Automation/State.php';
