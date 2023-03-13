<?php
declare(strict_types=1);

namespace App\Controller\Response;

use App\Model\Data\CourseStatusData;
use App\Model\Data\ModuleStatusData;

class CourseApiResponseFormatter
{
    public static function formatCourseStatusData(CourseStatusData $courseStatusData): array
    {
        $result = [
            'enrollment_id' => $courseStatusData->getEnrollmentId(),
            'progress' => $courseStatusData->getProgress(),
            'duration' => $courseStatusData->getDuration()
        ];
        $moduleStatusData = $courseStatusData->getModules();
        if (!empty($moduleStatusData))
        {
            $result['modules'] = self::formatModuleStatusData($moduleStatusData);
        }
        return $result;
    }

    /**
     * @param ModuleStatusData[] $moduleStatusData
     * @return array
     */

    private static function formatModuleStatusData(array $moduleStatusData): array
    {
        $result = [];
        foreach ($moduleStatusData as $data)
        {
            $result[] =  [
                'module_id' => $data->getModuleId(),
                'progress' => $data->getProgress()
                ];
        }
        return $result;
    }
}
