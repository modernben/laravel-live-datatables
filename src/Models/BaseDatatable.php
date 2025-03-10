<?php

namespace Modernben\LaravelLiveDatatables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;

class BaseDatatable extends Model
{

    public function scopeFilterIn($query, $filters, $boolean = 'or')
    {
        return $this->filter($filters, $boolean);
    }

    public function scopeFilterNotIn($query, $filters, $boolean = 'or')
    {
        return $this->filter($filters, $boolean, true);
    }

    public function scopeFilter($query, $filters, $boolean = 'or', $not = false)
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $filter => $values) {
            if ($values == [] || ! is_array($values)) {
                continue;
            }

            $query->where(function ($sub_query) use ($filter, $values, $boolean, $not) {
                $sub_query->whereIn($filter, $values, $boolean, $not);
            });
        }

        return $query;
    }

    public function scopeOrder($query, $order)
    {
        if (empty($order)) {
            return $query;
        }

        $key = str_replace('-', '', $order);
        $direction = Str::startsWith($order, '-') ? 'DESC' : 'ASC';

        return $query->orderBy($key, $direction);
    }

    public function scopeExport($query, $filename = null, $type = 'csv')
    {
        if ($filename == null) {
            $filename = $this->getTable();
        }

        if ($type == "xls") {
            $type = "xlsx";
        }

        $temp_file = Str::uuid() . '.' . $type;
        $writer = SimpleExcelWriter::create($temp_file, $type);
        $query->chunk(250, function ($rows) use ($writer) {
            foreach ($rows as $row) {
                $writer->addRow($row->export());
            }
        });

        return response()->download($temp_file, $filename . '.' . $type)->deleteFileAfterSend();
    }
}
