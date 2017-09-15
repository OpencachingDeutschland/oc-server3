<?php
namespace okapi\core\Exception;

use okapi\Settings;

# BEWARE: This class is also used in native OCPL code.

/** A base class for all bad request exceptions. */
class BadRequest extends \Exception
{
    protected function provideExtras(&$extras)
    {
        $extras['reason_stack'][] = 'bad_request';
        $extras['status'] = 400;
    }

    public function getOkapiJSON()
    {
        $extras = [
            'developer_message' => $this->getMessage(),
            'reason_stack' => [],
        ];
        $this->provideExtras($extras);
        $extras['more_info'] = Settings::get('SITE_URL') . "okapi/introduction.html#errors";

        return json_encode(["error" => $extras]);
    }
}
