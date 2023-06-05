<?php

namespace App\Filters;

use Carbon\Carbon;

class OrderFilters extends QueryFilter
{

    /**
     * @param $type
     * @return mixed
     */
    public function typeFilter( $type = '' )
    {
        if ( !empty( $type ) )
            return $this->builder->where( 'type', $type );
    }


    /**
     * @param $status
     * @return mixed
     */
    public function statusFilter( $status = '' )
    {
        if ( !empty( $status ) )
            return $this->builder->where( 'status', $status );
    }


    /**
     * @param $date
     * @return mixed
     */
    public function dateFilter( $date )
    {
        if ( empty( $date[ 'from' ] ) || empty( $date[ 'to' ] ) )
            return $this->builder;

        $from = Carbon::parse( $date[ 'from' ] );
        $to   = Carbon::parse( $date[ 'to' ] )->setTime( 19, 00, 00 );

        return $this->builder->whereBetween( 'deliver_at', [ $from, $to ] );
    }
    
    /**
     * @param string $term
     * @return mixed
     */
    public function term(?string $term = null)
    {
        if (is_null($term) || empty($term) || $term == '') {
            return $this->builder;
        }

        $term = trim(strtolower($term));

        return $this->builder->where('id', $term)
            ->orWhereHas('user', function ($user) use ($term) {
                return $user->whereRaw('LOWER(`username`) LIKE ?', ["%{$term}%"]);
            })
            ->orWhereHas('tids', function ($tid) use ($term) {
                return $tid->whereRaw('LOWER(`tid`) LIKE ?', ['%' . sprintf('%s.pdf', $term) . '%']);
            });
    }
}
