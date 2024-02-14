<?php

namespace App\Http\Controllers;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnumController extends Controller
{
    public function index(): JsonResponse
    {
        $enums = [];

        ClassFinder::disablePSR4Vendors();
        $classes = ClassFinder::getClassesInNamespace('App\Enums', ClassFinder::RECURSIVE_MODE);

        foreach ($classes as $class) {
            $enums[$this->getEnumName($class)] = $class::all();
        }

        return response()->json([
            'data' => $enums,
        ], Response::HTTP_OK);
    }

    public function getEnumName(string $namespace): string
    {
        $pos = strpos($namespace, 'Enums');
        $enumPart = substr($namespace, $pos + 6);
        $enumPart = str_replace('\\', '.', $enumPart);
        $enumPart = preg_replace('/([a-z])([A-Z])/', '$1-$2', $enumPart);
        $enumPart = strtolower($enumPart);

        return $enumPart;
    }
}
