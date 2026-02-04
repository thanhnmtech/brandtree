<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;
use Illuminate\Support\Arr;

class BrandTreeController extends Controller
{
    public function root(Brand $brand)
    {
        // Xây dựng rootSteps
        $rootSteps = $this->buildTimelineSteps(
            config('timeline_steps.root'),
            $brand->root_data ?? [],
            true // Root phase is always unlocked
        );

        // Kiểm tra root đã hoàn thành chưa để build trunkSteps
        // (Move this logic up so it's available for AJAX response too)
        $isRootFinished = !empty($brand->root_data['root3']);
        $trunkSteps = $this->buildTimelineSteps(
            config('timeline_steps.trunk'),
            $brand->trunk_data ?? [],
            $isRootFinished
        );

        if (request()->ajax()) {
            return response()->json([
                'html' => view('brands.trees.partials.root_steps', [
                    'rootSteps' => $rootSteps,
                    'brand' => $brand,
                ])->render(),
                'progress_html' => view('brands.trees.partials.progress_card', [
                    'phaseTitle' => 'Gốc Cây',
                    'steps' => $rootSteps,
                    'stepPrefix' => 'G',
                ])->render(),
                'next_step_html' => view('brands.trees.partials.next_step_card', [
                    'rootSteps' => $rootSteps,
                    'trunkSteps' => $trunkSteps,
                    'brand' => $brand,
                ])->render(),
            ]);
        }



        return view('brands.trees.root', [
            'brand' => $brand,
            'rootSteps' => $rootSteps,
            'trunkSteps' => $trunkSteps,
        ]);
    }

    public function trunk(Brand $brand)
    {
        // Xây dựng rootSteps để kiểm tra tiến trình
        $rootSteps = $this->buildTimelineSteps(
            config('timeline_steps.root'),
            $brand->root_data ?? [],
            true
        );

        $isRootFinished = !empty($brand->root_data['root3']);

        $trunkSteps = $this->buildTimelineSteps(
            config('timeline_steps.trunk'),
            $brand->trunk_data ?? [],
            $isRootFinished
        );

        if (request()->ajax()) {
            return response()->json([
                'html' => view('brands.trees.partials.trunk_steps', [
                    'trunkSteps' => $trunkSteps,
                    'brand' => $brand,
                ])->render(),
                'progress_html' => view('brands.trees.partials.progress_card', [
                    'phaseTitle' => 'Thân Cây',
                    'steps' => $trunkSteps,
                    'stepPrefix' => 'T',
                ])->render(),
                'next_step_html' => view('brands.trees.partials.next_step_card', [
                    'rootSteps' => $rootSteps,
                    'trunkSteps' => $trunkSteps,
                    'brand' => $brand,
                ])->render(),
            ]);
        }

        return view('brands.trees.trunk', [
            'brand' => $brand,
            'rootSteps' => $rootSteps,
            'trunkSteps' => $trunkSteps,
        ]);
    }

    public function canopy(Brand $brand): View
    {
        $agents = \App\Models\BrandAgent::where('brand_id', $brand->id)->latest()->get();
        $agentLibrary = \App\Models\AgentLibrary::where('is_active', true)->get();
        return view('brands.trees.canopy', compact('brand', 'agents', 'agentLibrary'));
    }

    private function buildTimelineSteps(array $stepsConfig, ?array $data, bool $isPhaseUnlocked): array
    {
        $data = $data ?? [];
        $steps = [];

        $isPreviousCompleted = $isPhaseUnlocked;

        foreach ($stepsConfig as $key => $config) {

            $hasData = isset($data[$key]) && !empty($data[$key]);

            if ($hasData) {
                $status = 'completed';
                $isPreviousCompleted = true;

            } elseif ($isPreviousCompleted) {
                $status = 'ready';
                $isPreviousCompleted = false;

            } else {
                $status = 'locked';
            }

            $steps[] = [
                'key' => $key,
                'status' => $status,
                'is_current' => $status === 'ready',
                'data' => $hasData ? $data[$key] : null,

                'label' => Arr::get($config, 'label'),
                'label_short' => Arr::get($config, 'label_short'),
                'description' => Arr::get($config, 'description'),
            ];
        }
        return $steps;
    }
}
