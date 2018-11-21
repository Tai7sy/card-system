const fetch = window.$http;

export function getList (params) {
  return fetch({
    url: 'product',
    method: 'post',
    data: params
  });
}

export function category_edit (id, category_id) {
  return fetch({
    url: 'product/category',
    method: 'post',
    data: { id, category_id }
  });
}

export function edit (params) {
  return fetch({
    url: 'product/edit',
    method: 'post',
    data: params
  });
}


export function enable (ids, enabled) {
  return fetch({
    url: 'product/enable',
    method: 'post',
    data: { ids, enabled }
  });
}

export function sort (id, sort) {
  return fetch({
    url: 'product/sort',
    method: 'post',
    data: { id, sort }
  });
}


export function deleteAction (ids) {
  return fetch({
    url: 'product/delete',
    method: 'post',
    data: { ids }
  });
}
