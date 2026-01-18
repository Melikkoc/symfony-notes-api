<?php
namespace App\Service;

class AppInfoService {
    public function getVersion(): array {
       return [
            'name' => 'setupschmiede_be', 
            'version' => '0.1.0',
        ];
    }
}