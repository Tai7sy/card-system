import fetch from '../utils/fetch';

const orderStatus = {
  UN_PAY: {
    id: 0,
    name: '未支付',
    type: 'info'
  },
  PAID: {
    id: 1,
    name: '待发货',
    type: 'primary' // 高亮
  },
  SUCCESS: {
    id: 2,
    name: '已成功',
    type: 'success' // 高亮
  },
  FROZEN: {
    id: 3,
    name: '已冻结',
    type: 'danger' // 高亮
  },
  REFUND: {
    id: 4,
    name: '已退款',
    type: 'warning' // 高亮
  },

  get (id) {
    for (const key in this) {
      if (this.hasOwnProperty(key) && this[key].id === id) {
        return this[key]
      }
    }
    return {
      id,
      type: 'danger',
      name: '未知状态'
    };
  }
};

export { orderStatus }

export function stat (params) {
  return fetch({
    url: 'order/stat',
    method: 'post',
    params
  });
}

export function getList (params) {
  return fetch({
    url: 'order',
    method: 'post',
    data: params
  });
}


export function editRemark (id, remark) {
  return fetch({
    url: 'order/remark',
    method: 'post',
    data: { id, remark }
  });
}

export function ship (id) {
  return fetch({
    url: 'order/ship',
    method: 'post',
    data: { id }
  });
}

export function notify (id) {
  return fetch({
    url: 'order/notify',
    method: 'post',
    data: { id }
  });
}


export function getTodayIncome (user_id) {
  return fetch({
    url: 'order/income/today',
    method: 'post',
    data: { user_id }
  });
}


export function deleteAction (ids, delete_income, delete_balance) {
  return false;
}

export function freeze (ids, reason) {
  return false;
}


export function unfreeze (ids) {
  return false;
}

export function setPaid (id, trade_no) {
  return false;
}

export function info (id) {
  return fetch({
    url: 'order/info',
    method: 'post',
    data: { id }
  });
}
