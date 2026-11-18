'use strict';
const grid = document.querySelector('#product-grid');
const cards = [...grid.querySelectorAll('.card')];
const filters = [...document.querySelectorAll('.filter')];
const search = document.querySelector('#search');
const sort = document.querySelector('#sort');
let category = 'all';
const normalize = text => text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
function refresh() {
  const query = normalize(search.value.trim());
  let count = 0;
  const ordered = [...cards];
  if (sort.value === 'low') ordered.sort((a,b) => Number(a.dataset.price)-Number(b.dataset.price));
  if (sort.value === 'high') ordered.sort((a,b) => Number(b.dataset.price)-Number(a.dataset.price));
  if (sort.value === 'name') ordered.sort((a,b) => a.dataset.name.localeCompare(b.dataset.name,'es'));
  ordered.forEach(card => {
    card.hidden = !((category === 'all' || card.dataset.category === category) && normalize(card.dataset.search).includes(query));
    if (!card.hidden) count++;
    grid.append(card);
  });
  document.querySelector('#result-count').textContent = `${count} componente${count === 1 ? '' : 's'}`;
  document.querySelector('#no-results').hidden = count !== 0;
}
filters.forEach(button => button.addEventListener('click', () => {
  category = button.dataset.category;
  filters.forEach(b => { b.classList.toggle('active', b === button); b.setAttribute('aria-pressed', String(b === button)); });
  refresh();
}));
search.addEventListener('input', refresh);
sort.addEventListener('change', refresh);
document.querySelector('#catalog-tools').hidden = false;
refresh();
