<?php

namespace Trisnm\Userstamps;

trait Userstamps
{
    /**
     * Whether we're currently maintaing userstamps.
     *
     * @param bool
     */
    protected $userstamping = true;

    /**
     * Boot the userstamps trait for a model.
     *
     * @return void
     */
    public static function bootUserstamps()
    {
        static::addGlobalScope(new UserstampsScope);

        static::registerListeners();
    }

    /**
     * Register events we need to listen for.
     *
     * @return void
     */
    public static function registerListeners()
    {
        static::creating('Trisnm\Userstamps\Listeners\Creating@handle');
        static::saving('Trisnm\Userstamps\Listeners\Saving@handle');
        static::updating('Trisnm\Userstamps\Listeners\Updating@handle');

        if (static::usingSoftDeletes()) {
            static::deleting('Trisnm\Userstamps\Listeners\Deleting@handle');
            static::restoring('Trisnm\Userstamps\Listeners\Restoring@handle');
        }
    }

    /**
     * Has the model loaded the SoftDeletes trait.
     *
     * @return bool
     */
    public static function usingSoftDeletes()
    {
        static $usingSoftDeletes;

        if (is_null($usingSoftDeletes)) {
            return $usingSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(get_called_class()));
        }

        return $usingSoftDeletes;
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    /**
     * Check if we're maintaing Userstamps on the model.
     *
     * @return bool
     */
    public function isUserstamping()
    {
        return $this->userstamping;
    }

    /**
     * Stop maintaining Userstamps on the model.
     *
     * @return void
     */
    public function stopUserstamping()
    {
        $this->userstamping = false;
    }

    /**
     * Start maintaining Userstamps on the model.
     *
     * @return void
     */
    public function startUserstamping()
    {
        $this->userstamping = true;
    }

    /**
     * Get the class being used to provide a User.
     *
     * @return string
     */
    protected function getUserClass()
    {
        return config('auth.providers.users.model');
    }

    /**
     * getTargetUser
     *
     * @return void
     */
    public function getTargetUser()
    {
        if (!auth()->user()) {
            return null;
        }

        $user = auth()->user();
        return  sprintf('%s_%d', get_class($user), $user->id);
    }
}
