@php
    $imageArray = [
        asset('images/about-icon_001.png'),
        asset('images/about-icon_002.png'),
        asset('images/about-icon_003.png')
    ];
@endphp
<div class="about-tabs-content">
    <ul>
        @foreach($banner['employer'] as $key => $item)
            <li>
                <div class="about-tab-image" style="background-image:url({{ asset("uploads/banner/$item->banner_image") }})">
                </div>
                <div class="about-tab-desc">
                    <span class="about-tab-icon">
                        <img src="{{ $imageArray[$key] }}">                        
                    </span>
                    <h5>{{$item->banner_title}}</h5>
                    <p>{!!nl2br($item->banner_text)!!}</p>
                </div>
            </li>
        @endforeach
    </ul>
</div>