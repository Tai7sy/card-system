import fetch from '~/utils/fetch';

const ID_INNER_RETAIL = -1;

export { ID_INNER_RETAIL }

export function stat (params) {
  return fetch({
    url: 'pay/stat',
    method: 'get',
    params
  });
}

export function getList (params) {
  return fetch({
    url: 'pay',
    method: 'get',
    params
  });
}

export function edit (data) {
  return fetch({
    url: 'pay/edit',
    method: 'post',
    data
  });
}


export function enable (ids, enabled) {
  return fetch({
    url: 'pay/enable',
    method: 'post',
    data: {
      ids, enabled
    }
  });
}

export function fee_system (id, fee_system) {
  return fetch({
    url: '/pay/fee_system',
    method: 'post',
    data: {
      id, fee_system
    }
  });
}

export function fee (id, fee) {
  return fetch({
    url: '/pay/fee',
    method: 'post',
    data: {
      id, fee
    }
  });
}

export function edit_comment (id, comment) {
  return fetch({
    url: '/pay/comment',
    method: 'post',
    data: {
      id, comment
    }
  });
}

export function delete_ (ids) {
  return fetch({
    url: 'pay/delete',
    method: 'post',
    data: { ids }
  });
}
