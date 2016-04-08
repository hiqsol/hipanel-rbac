<?php

/*
 * RBAC implementation for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-rbac
 * @package   hipanel-rbac
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\rbac;

use yii\base\InvalidParamException;
use yii\rbac\Item;

/**
 * AuthManager class.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class AuthManager extends \yii\rbac\PhpManager
{
    /**
     * Set permission.
     * @param string $name
     * @param string $description
     * @return Item
     */
    public function setPermission($name, $description = null)
    {
        return $this->setItem('permission', $name, $description);
    }

    /**
     * Set role.
     * @param string $name
     * @param string $description
     * @return Item
     */
    public function setRole($name, $description = null)
    {
        return $this->setItem('role', $name, $description);
    }

    /**
     * Set item by type and name.
     * Created if not exists else updates.
     * @param string $type
     * @param string $name
     * @param string $description
     * @return Item
     */
    public function setItem($type, $name, $description = null)
    {
        $item = $this->getItem($name) ?: $this->createItem($type, $name);
        if ($description) {
            $item->description = $description;
        }
        $this->add($item);

        return $item;
    }

    /**
     * Create item by type and name.
     * @param string $type
     * @param string $name
     * @throws InvalidParamException
     * @return Item
     */
    public function createItem($type, $name)
    {
        if ('role' === $type) {
            return $this->createRole($name);
        } elseif ('permission' === $type) {
            return $this->createPermission($name);
        } else {
            throw new InvalidParamException('Creating unsupported item type: ' . $type);
        }
    }

    /**
     * Set child.
     * @param string|Item $parent
     * @param string|Item $child
     * @return bool
     */
    public function setChild($parent, $child)
    {
        if (is_string($parent)) {
            $name   = $parent;
            $parent = $this->getItem($parent);
            if (is_null($parent)) {
                throw new InvalidParamException("Unknown parent:$name at setChild");
            }
        }
        if (is_string($child)) {
            $name  = $child;
            $child = $this->getItem($child);
            if (is_null($child)) {
                throw new InvalidParamException("Unknown child:$name at setChild");
            }
        }
        if (isset($this->children[$parent->name][$child->name])) {
            return false;
        }
        return $this->addChild($parent, $child);
    }

    public function setAssignment($role, $userId)
    {
        if (is_string($role)) {
            $name = $role;
            $role = $this->getRole($role);
            if (is_null($role)) {
                throw new InvalidParamException("Unknown role:$name at setAssignment");
            }
        }
        if (isset($this->assignments[$userId][$role->name])) {
            return false;
        }
        return $this->assign($role, $userId);
    }
}