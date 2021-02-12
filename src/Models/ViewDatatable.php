<?php

namespace Modernben\LaravelLiveDatatables\Models;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

abstract class ViewDatatable extends Component
{
    use WithPagination;

    /**
     * Handles weather the table has a search.
     *
     * @var bool
     */
    public $hasSearch = true;

    /**
     * Search query.
     *
     * @var string
     */
    public $search = '';

    /**
     * Default for how many records to show.
     *
     * @var int
     */
    public $perPage = 10;

    /**
     * Default for pages dropdown.
     *
     * @var array
     */
    public $pages = [
        '10',
        '25',
        '50',
        '100',
        '1000',
    ];

    /**
     * Parameters to include in query string.
     *
     * @var array
     */
    protected $queryString = ['perPage', 'search', 'sortField'];

    /**
     * The filters for the results
     */
    public $filters = [];

    protected $paginationTheme = 'bootstrap';

    public $sortField = null;

    public function mount()
    {
        $this->perPage = request()->perPage ?? $this->perPage;
        $this->search = request()->search ?? $this->search;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            if (Str::startsWith($this->sortField, '-')) {
                $this->sortField = ltrim($this->sortField, '-');
            } else {
                $this->sortField = '-' . $this->sortField;
            }
        } else {
            $this->sortField = $field;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }
}
