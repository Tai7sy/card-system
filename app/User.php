<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable, \App\Concerns\SerializesLegacyDates;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $appends = ['m_balance', 'role'];

    protected $casts = [
        'theme_config' => 'array'
    ];

    const ID_CUSTOMER = -1;

    // 商户库存显示类型
    /** @var int 大致范围 */
    const INVENTORY_RANGE = 0;
    /** @var int 实际库存 */
    const INVENTORY_REAL = 1;
    /** @var int 只用于商品属性: 跟随店铺 */
    const INVENTORY_AUTO = 2;

    /** 卖家承担 */
    const FEE_TYPE_MERCHANT = 0;
    /** 买家承担 */
    const FEE_TYPE_CUSTOMER = 1;
    /** @var int 只用于商品属性: 跟随店铺 */
    const FEE_TYPE_AUTO = 2;


    const STATUS_OK = 0;
    const STATUS_FROZEN = 1;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Sends the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        throw new \Exception('unimplemented in free version');
    }

    function getMBalanceAttribute()
    {
        return $this->m_all - $this->m_paid - $this->m_frozen;
    }

    function getRoleAttribute(){
        return 'admin';
    }

    function getMBalanceWithoutTodayAttribute()
    {
        $income_today = (int)\App\Order::where('user_id', $this->user_id)
            ->where('status', \App\Order::STATUS_SUCCESS)->whereDate('paid_at', Carbon::today())->sum('income');
        return $this->m_all - $this->m_paid - $this->m_frozen - $income_today;
    }

    /**
     * @return ShopTheme
     */
    function getShopThemeAttribute()
    {
        if ($this->theme_config && isset($this->theme_config['theme'])) {
            $ret = \App\ShopTheme::whereName($this->theme_config['theme'])->first();
            if ($ret) return $ret;
        }
        return \App\ShopTheme::defaultTheme();
    }

    function getLastLoginAtAttribute()
    {
        /** @var \App\Log $last_login_log */
        $last_login_log = $this->logs()->where('action', \App\Log::ACTION_LOGIN)->orderBy('id', 'DESC')->first();
        return $last_login_log ? ($last_login_log->address ?? $last_login_log->ip) : null;
    }

    function categories()
    {
        return $this->hasMany(Category::class);
    }

    function products()
    {
        return $this->hasMany(Product::class);
    }

    function cards()
    {
        return $this->hasMany(Card::class);
    }

    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    function logs()
    {
        return $this->hasMany(Log::class);
    }

    function shop_theme()
    {
        return $this->belongsTo(ShopTheme::class);
    }
}
