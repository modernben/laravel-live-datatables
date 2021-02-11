<?php

namespace Modernben\LaravelLiveDatatables\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait Searchable
{
    /**
     * Search utility for models
     * Contact::search('ben')->get() // searches Contact::searchFields for 'ben'
     * Contact::search('ben', ['first_name'])->get() // searches `first_name` for 'ben'
     *
     * Using a custom relationship search
     * Contact::search('916', ['phoneNumbers' => 'number'])->get() // uses a ['relationship' => 'field']
     *
     * Searching fields from a join
     * Contact::search('Tom', 'users.first_name')->get() // uses the table.field structure
     */
    public function scopeSearch($query, $search, $fields = [])
    {
        if (empty($search)) {
            // short circuit the query if the term is blank
            return $query->where(DB::raw('0 = 1'));
        }

        // SANITIZE USER INPUT!
        // $search_terms = explode(' ', $search);
        $search_terms = Arr::wrap($search);

        foreach($search_terms as $index => $search) {
            $search_terms[$index] = '%'.trim(htmlentities($search, ENT_QUOTES, 'UTF-8', false)).'%';
        }

        if (! empty($fields)) {
            $searchFields = $fields;
        } elseif (property_exists($this, 'searchFields')) {
            $searchFields = $this->searchFields;
        } else {
            $searchFields = ['uuid'];
        }

        $query->where(function ($query) use ($search_terms, $searchFields) {
            foreach($search_terms as $search) {
                foreach ($searchFields as $field) {

                    // setup relationship searching
                    if (is_array($field)) {
                        $relationship_name = array_keys($field)[0];
                        $field = array_values($field)[0];

                        if (method_exists($this, $relationship_name)) {
                            $updated_search = $search;
                            if ($relationship_name == 'phoneNumbers') {
                                if ( Str::contains($search, '@') ) {
                                    continue;
                                }

                                $updated_search = '%'.preg_replace('/[^0-9]/', '', $search).'%';
                                if ($updated_search == '%%') {
                                    continue;
                                }
                            }

                            $query->orWhereHas($relationship_name, function ($query) use ($field, $updated_search) {
                                $query->where($field, 'LIKE', $updated_search);
                            });
                        }
                    } else if ( in_array($field, ["users.name", "module_crm_contacts.name", "module_leads.name"]) ) {
                        // search first_name + last_name at once
                        $table = explode('.', $field)[0];
                        $query->orWhereRaw("CONCAT({$table}.first_name, ' ', {$table}.last_name) LIKE ?", $search);
                    }else {
                        $query->orWhere($field, 'LIKE', $search);
                    }
                }
            }
        });

        return $query;
    }
}
