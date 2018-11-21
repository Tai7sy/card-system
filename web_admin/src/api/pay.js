import fetch from '../utils/fetch';

const paywayEnabled = {
  DISABLED: {
    id: 0,
    name: '禁用',
    type: 'info'
  },
  PC: {
    id: 1,
    name: '电脑端',
    type: 'primary'
  },
  MOBILE: {
    id: 2,
    name: '手机端',
    type: 'primary'
  },
  PC_MOBILE: {
    id: 3, // 位运算 = 1|2 = 3
    name: '电脑+手机',
    type: 'primary'
  },
  getEnabled (id) {
    for (const key in this) {
      if (this.hasOwnProperty(key) && this[key].id === id) {
        return this[key]
      }
    }
    return { id, name: '未知状态', type: 'error' };
  }
};

export { paywayEnabled }

export function stat (params) {
  return fetch({
    url: 'pay/stat',
    method: 'post',
    params
  });
}

export function getList (params) {
  return fetch({
    url: 'pay',
    method: 'post',
    data: params
  });
}

export function edit (params) {
  return fetch({
    url: 'pay/edit',
    method: 'post',
    data: params
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

export function sort (id, sort) {
  return fetch({
    url: 'pay/sort',
    method: 'post',
    data: {
      id, sort
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

export function deletePayway (id) {
  return fetch({
    url: 'pay/delete',
    method: 'post',
    data: { id }
  });
}
