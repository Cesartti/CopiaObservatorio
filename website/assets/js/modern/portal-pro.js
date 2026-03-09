(function(){
  const marketData=[
    {label:'TRM',value:'$ 3.795,55',delta:'+0,73%',up:true},
    {label:'USD/COP',value:'$ 3.812,40',delta:'+0,42%',up:true},
    {label:'IPC Mensual',value:'1,08%',delta:'-0,10%',up:false},
    {label:'Desempleo',value:'10,9%',delta:'-0,3 pp',up:true},
    {label:'Petróleo Brent',value:'US$ 92,40',delta:'+8,95%',up:true},
    {label:'Oro',value:'US$ 2.153,10',delta:'+1,44%',up:true}
  ];

  const track=document.getElementById('marketTickerTrack');
  if(track){
    const row=[...marketData,...marketData].map(i=>`<div class="market-item"><strong>${i.label}</strong><span>${i.value}</span><span class="${i.up?'up':'down'}">${i.up?'▲':'▼'} ${i.delta}</span></div>`).join('');
    track.innerHTML=row;
  }

  const input=document.getElementById('globalSearch');
  const button=document.getElementById('searchButton');
  const results=document.getElementById('searchResults');


  async function loadGeneralNews(){
    const container=document.getElementById('generalNewsCards');
    if(!container) return;
    try{
      const response=await fetch('api/content.php');
      const data=await response.json();
      const items=data.general_news||[];
      if(!items.length) return;
      container.innerHTML=items.slice(0,4).map(n=>`<div class="col-md-6 col-xl-3"><article class="news-card"><span>Noticia</span><h3>${n.title}</h3><p>${n.summary||''}</p></article></div>`).join('');
    }catch(_e){}
  }

  async function runSearch(){
    const q=(input.value||'').trim();
    if(!q){results.innerHTML='';return;}
    const res=await fetch('api/search.php?q='+encodeURIComponent(q));
    const json=await res.json();
    const items=json.items||[];
    results.innerHTML=items.length?items.map(it=>`<div class="search-item"><a href="${it.url}"><strong>${it.title}</strong></a><div>${it.context||''}</div></div>`).join(''):'<div class="search-item">Sin resultados.</div>';
  }

  if(button){button.addEventListener('click',runSearch)}
  if(input){input.addEventListener('keydown',e=>{if(e.key==='Enter')runSearch()})}
  loadGeneralNews();
})();
