<?php

namespace App\Http\Livewire;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class AllNews extends Component
{
    public Collection $blogs;
    public $search = '';
    
    // Lazy loading properties
    public $pageNumber = 1;
    public $perPage = 6;
    public $hasMorePages = true;

    public function mount()
    {
        $this->blogs = new Collection();
        $this->loadMore();
    }

    public function updateSearch()
    {
        $this->resetItems();
    }
    
    private function getQuery()
    {
        return News::where('title', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc');
    }
    
    public function loadMore(): void
    {
        $paginated = $this->getQuery()->paginate($this->perPage, ['*'], 'page', $this->pageNumber);
        
        $this->pageNumber++;
        $this->hasMorePages = $paginated->hasMorePages();
        
        // Append new items to the collection
        $this->blogs = $this->blogs->merge($paginated->items());
    }
    
    private function resetItems(): void
    {
        $this->blogs = new Collection();
        $this->pageNumber = 1;
        $this->hasMorePages = true;
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.news');
    }
}
