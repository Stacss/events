<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      title="EVENTS - тестовый проект",
 *      version="1.0.0",
 *      description="API управления Событиями проекта",
 *      @OA\Contact(
 *          email="postnikov.sa@ya.ru",
 *          name="Stanoslav"
 *      )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
