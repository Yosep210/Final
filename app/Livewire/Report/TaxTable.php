<?php

namespace App\Livewire\Report;

use App\Models\CommissionLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class TaxTable extends PowerGridComponent
{
    public string $tableName = 'taxTable';

    public string $sortField = 'tax_month';

    public string $sortDirection = 'desc';

    public string $primaryKey = 'member_id';

    public function setUp(): array
    {
        return [
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $allowedSort = [
            'tax_month' => 'tax_month',
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'total_bonus' => 'total_bonus',
            'total_tax' => 'total_tax',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'tax_month';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'tax_month' => "DATE_FORMAT(commission_logs.created_at, '%Y-%m')",
            'total_bonus' => 'SUM(commission_logs.gross_commission)',
            'total_tax' => 'SUM(commission_logs.tax_amount)',
        ];
        $windowOrder = $windowOrderMap[$sortColumn] ?? $sortColumn;

        return CommissionLog::query()
            ->join('members as member', 'commission_logs.member_id', '=', 'member.id')
            ->leftJoin('member_profile as profile', 'member.id', '=', 'profile.member_id')
            ->select([
                'commission_logs.member_id',
                DB::raw("DATE_FORMAT(commission_logs.created_at, '%Y-%m') as tax_month"),
                DB::raw('SUM(commission_logs.gross_commission) as total_bonus'),
                DB::raw('SUM(commission_logs.tax_amount) as total_tax'),
                'member.username as member_username',
                'member.name as member_name',
                'profile.id_card_number as member_id_card',
            ])
            ->groupBy(
                'commission_logs.member_id',
                DB::raw("DATE_FORMAT(commission_logs.created_at, '%Y-%m')"),
                'member.username',
                'member.name',
                'profile.id_card_number'
            )
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('tax_month_formatted', function ($row) {
                return Carbon::parse($row->tax_month.'-01')->locale('id')->isoFormat('MMMM YYYY');
            })
            ->add('username', fn ($row) => strtoupper($row->member_username ?? ''))
            ->add('name', fn ($row) => strtoupper($row->member_name ?? ''))
            ->add('id_card_number', fn ($row) => $row->member_id_card ?: '-')
            ->add('total_bonus_formatted', fn ($row) => 'Rp '.number_format($row->total_bonus, 0))
            ->add('total_tax_formatted', fn ($row) => 'Rp '.number_format($row->total_tax, 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Bulan', 'tax_month_formatted', 'tax_month')->sortable(),
            Column::make('Username', 'username', 'member.username')->sortable(),
            Column::make('Nama Member', 'name', 'member.name')->sortable(),
            Column::make('No. Identitas (KTP)', 'id_card_number'),
            Column::make('Total Bonus (Gross)', 'total_bonus_formatted', 'total_bonus')->sortable(),
            Column::make('Potongan Pajak', 'total_tax_formatted', 'total_tax')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('username')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.name', 'like', '%'.$searchTerm.'%');
                }),
        ];
    }
}
