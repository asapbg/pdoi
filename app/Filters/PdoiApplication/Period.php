<?php

namespace App\Filters\PdoiApplication;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;
use App\Models\User;
use Carbon\Carbon;


class Period extends QueryFilter implements FilterContract{

    public function handle($value): void
    {
        if( !empty($value) ){
            switch ($value)
            {
                case 'today':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->startOfDay())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->endOfDay());
                    });
                    break;
                case 'yesterday':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->startOfDay()->subDays(1))
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->endOfDay()->subDays(1));
                    });
                    break;
                case 'current_month':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->startOfMonth())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->endOfMonth());
                    });
                    break;
                case 'prev_week':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->subDays(7)->startOfWeek())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->subDays(7)->endOfWeek());
                    });
                    break;
                case 'prev_month':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->subMonths(1)->startOfMonth())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->subMonths(1)->endOfMonth());
                    });
                    break;
                case 'current_year':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->startOfYear())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->endOfYear());
                    });
                    break;
                case 'prev_year':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->subYears(1)->startOfYear())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->subYears(1)->endOfYear());
                    });
                    break;
                case 'last_5_year':
                    $this->query->where(function ($q){
                        return $q->where('pdoi_application.created_at', '>=', Carbon::now()->subYears(4)->startOfYear())
                            ->where('pdoi_application.created_at', '<=', Carbon::now()->subYears(4)->endOfYear());
                    });
                    break;
            }
        }
    }
}
