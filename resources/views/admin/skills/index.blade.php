@extends('admin.layout')
@section('title', 'Skills')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

    {{-- Categories + Skills list --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem">
        @foreach($categories as $cat)
        <div class="card">
            <div class="card-header">
                <span class="card-title">{{ $cat->icon }} {{ $cat->name }} <span class="badge badge-muted">{{ $cat->type }}</span></span>
                <form method="POST" action="{{ route('admin.skills.category.delete', $cat->id) }}" onsubmit="return confirm('Delete category and all its skills?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            <div class="card-body">
                @if(empty($cat->skills))
                    <p style="color:var(--muted);font-size:0.85rem">No skills yet.</p>
                @else
                    <div style="display:flex;flex-direction:column;gap:0.4rem;margin-bottom:1rem">
                        @foreach($cat->skills as $skill)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.4rem 0.6rem;background:rgba(255,255,255,0.02);border-radius:6px">
                            <span style="font-size:0.875rem">{{ $skill->name }} @if($skill->percentage)<span style="color:var(--cyan);font-family:'JetBrains Mono',monospace;font-size:0.75rem">&nbsp;{{ $skill->percentage }}%</span>@endif</span>
                            <form method="POST" action="{{ route('admin.skills.delete', $skill->id) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:0.2rem 0.5rem"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add skill to this category --}}
                <form method="POST" action="{{ route('admin.skills.store') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap">
                    @csrf
                    <input type="hidden" name="skill_category_id" value="{{ $cat->id }}">
                    <input type="text" name="name" class="form-control" placeholder="Skill name" style="flex:1;min-width:120px" required>
                    @if($cat->type === 'bars')
                        <input type="number" name="percentage" class="form-control" placeholder="%" style="width:70px" min="0" max="100">
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Add Category --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">Add Skill Category</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.skills.category.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Backend Development">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Icon (emoji)</label>
                            <input type="text" name="icon" class="form-control" placeholder="⚙️" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-control">
                                <option value="bars">Progress Bars</option>
                                <option value="tags">Tags</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Create Category</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
