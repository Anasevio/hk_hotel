{{-- resources/views/ra/partials/checklist-item.blade.php
     Dipakai di task-detail.blade.php
     Variabel: $item (TaskChecklist), $task (Task)
--}}

@php
    $canClick = $task->status === 'in_progress';
@endphp

<div class="cl-item {{ $item->is_checked ? 'checked' : '' }} {{ !$canClick ? 'readonly' : '' }}"
     id="cl-item-{{ $item->id }}"
     @if($canClick) onclick="toggleCheck({{ $item->id }}, this)" @endif>
    <div class="cl-box"></div>
    <span>{{ $item->item_name }}</span>
    @if($item->estimated_minutes)
        <span style="font-size:10px;color:var(--text-muted);margin-left:auto;white-space:nowrap">
            ~{{ $item->estimated_minutes }}m
        </span>
    @endif
</div>