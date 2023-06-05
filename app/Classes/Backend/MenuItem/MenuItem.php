<?php

    namespace App\Classes\Backend\MenuItem;

    class MenuItem implements IMenuItem
    {
        public function __construct(array $data) {
            $this->setPosition($data['position'] ?? -1);
            $this->setParent($data['parent'] ?? null);
            $this->setName($data['name'] ?? '');
            $this->setText($data['text'] ?? '');
            $this->setIcon($data['icon'] ?? '');
            $this->setRedirect($data['redirect'] ?? '');
            $this->setRouteName($data['route_name'] ?? '');
        }

        public function getPosition() : int {
            return $this->position ?? -1;
        }

        public function setPosition(int $position) : MenuItem {
            $this->position = $position;

            return $this;
        }

        public function getParent() : string {
            return $this->parent ?? null;
        }

        public function setParent($parent) : MenuItem {
            $this->parent = $parent;

            return $this;
        }

        public function getName() : string {
            return $this->name ?? '';
        }

        public function setName(string $name) : MenuItem {
            $this->name = $name;

            return $this;
        }

        public function getIcon() : string {
            return $this->icon ?? '';
        }

        public function setIcon(string $icon) : MenuItem {
            $this->icon = $icon;

            return $this;
        }

        public function getText() : string {
            return $this->text ?? '';
        }

        public function setText(string $text) : MenuItem {
            $this->text = $text;

            return $this;
        }

        public function getRedirect() : string {
            return $this->redirect ?? '';
        }

        public function setRedirect(string $redirect) : MenuItem {
            $this->redirect = $redirect;

            return $this;
        }

        public function getRouteName() : string {
            return $this->route_name ?? '';
        }

        public function setRouteName(string $route_name) : MenuItem {
            $this->route_name = $route_name;

            return $this;
        }
    }
