const fetch = window.$http;

export function getList (params) {
  return fetch({
    url: 'category',
    method: 'post',
    data: params
  });
}


export function edit (params) {
  return fetch({
    url: 'category/edit',
    method: 'post',
    data: params
  });
}


export function enable (ids, enabled) {
  return fetch({
    url: 'category/enable',
    method: 'post',
    data: { ids, enabled }
  });
}

export function sort (id, sort) {
  return fetch({
    url: 'category/sort',
    method: 'post',
    data: { id, sort }
  });
}

export function deleteAction (ids) {
  return fetch({
    url: 'category/delete',
    method: 'post',
    data: { ids }
  });
}
