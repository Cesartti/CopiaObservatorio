(function(){
  const marketData=[
    {label:'TRM',value:'$ 3.795,55',delta:'+0,73%',up:true},
    {label:'Dólar spot',value:'$ 3.812,40',delta:'+0,42%',up:true},
    {label:'Euro',value:'$ 4.394,68',delta:'+0,70%',up:true},
    {label:'IPC',value:'1,08%',delta:'-0,10%',up:false},
    {label:'Tasa intervención',value:'10,25%',delta:'+1,00%',up:true},
    {label:'Brent',value:'US$ 92,40',delta:'+8,95%',up:true}
  ];

  const track=document.getElementById('marketTickerTrack');
  if(track){
    track.innerHTML=[...marketData,...marketData].map(i=>`<div class="market-item"><strong>${i.label}</strong><span>${i.value}</span><span class="${i.up?'up':'down'}">${i.up?'▲':'▼'} ${i.delta}</span></div>`).join('');
  }

  const slug=window.OBS_SLUG;
  const prefixMap={economico:'1',social:'2',ambiente:'3',cti:'4',genero:'5'};
  const prefix=prefixMap[slug]||'';
  const list=document.getElementById('featuredIndicators');
  const totalKpi=document.getElementById('kpi-total');

  async function loadIndicators(){
    try{
      const response=await fetch('api/indicators.php');
      const json=await response.json();
      const all=json.items||[];
      const filtered=prefix?all.filter(item=>String(item.id).startsWith(prefix)):all;
      if(totalKpi) totalKpi.textContent=filtered.length;
      const top=filtered.slice(0,6);
      if(list){
        list.innerHTML=top.length?top.map(item=>`<li class="list-group-item"><a href="${item.url}"><strong>${item.titulo}</strong></a><div>${item.categoria||'Sin categoría'} / ${item.subcategoria||'Sin subcategoría'}</div></li>`).join(''):'<li class="list-group-item">No hay indicadores para mostrar.</li>';
      }
    }catch(e){
      if(list) list.innerHTML='<li class="list-group-item">No se pudo cargar el listado.</li>';
    }
  }

  async function loadDimensionNews(){
    const container=document.querySelector('.news-list');
    if(!container||!slug) return;
    try{
      const response=await fetch('api/content.php?slug='+encodeURIComponent(slug));
      const data=await response.json();
      const items=data.dimension_news||[];
      if(items.length){
        container.innerHTML=items.slice(0,4).map(n=>`<div><strong>${n.title}</strong><span>${n.date||''}</span></div>`).join('');
      }
    }catch(_e){}
  }

  function initGenderModule(){
    const chips=[...document.querySelectorAll('.chip-group .chip[data-target]')];
    const mods=[...document.querySelectorAll('.gender-mod[data-sec]')];
    if(!chips.length||!mods.length) return;
    chips.forEach(chip=>{
      chip.addEventListener('click',()=>{
        const target=chip.dataset.target;
        chips.forEach(c=>c.classList.remove('is-active'));
        chip.classList.add('is-active');
        mods.forEach(m=>m.classList.toggle('d-none',m.dataset.sec!==target));
      });
    });
  }

  loadIndicators();
  loadDimensionNews();
  initGenderModule();
})();
