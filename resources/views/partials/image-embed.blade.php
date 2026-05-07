@php
    use App\Models\Concerns\HasEmbeds;
    $urls = $model->extractUrls(4);
@endphp

@if(count($urls) > 0)
<div class="embed-grid" data-embed-count="{{ count($urls) }}">
    @foreach($urls as $rawUrl)
    @php $resolvedUrl = HasEmbeds::resolveImageUrl($rawUrl); @endphp
    <div class="embed-item"
        data-url="{{ e($resolvedUrl) }}"
        data-original-url="{{ e($rawUrl) }}">
    </div>
    @endforeach
</div>
@endif
