
const USER_STATUS = {
  OK: {
    id: 0,
    name: '正常',
    type: 'primary' // 高亮
  },
  FROZEN: {
    id: 1,
    name: '已冻结',
    type: 'warning'
  },
  get (id) {
    for (const key in this) {
      if (Object.prototype.hasOwnProperty.call(this, key) && this[key].id === id) {
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
export { USER_STATUS }

const INVENTORY_TYPE = {
  RANGE: 0, // 大致范围
  REAL: 1, // 实际库存
  AUTO: 2, 
};

export { INVENTORY_TYPE }
