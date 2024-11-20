 <div class="updates-container mt-1 mb-1">
                  <div class="updates-title">Updates</div>
                  <div class="marquee-content">
                     <div class="marquee-inner">
                           @foreach($newsItems as $newsItem)
                           {{ $newsItem->title }} - {{ $newsItem->content }}
                           @if (!$loop->last)
                             &#x2022;
                           @endif
                        @endforeach
                     </div>
                  </div>
               </div>
