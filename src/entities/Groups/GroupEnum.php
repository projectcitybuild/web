<?php
namespace Entities\Groups;

final class GroupEnum extends Enum {

    const Administrator = 'administrator';
    const SeniorOperator = 'senior operator';
    const Operator = 'operator';
    const Moderator = 'moderator';
    const Donator = 'donator';
    const Trusted = 'trusted';
    const Member = 'member';
    const Retired = 'retired';

    public function discourseId() : int {
        switch ($this->value) {
            case self::Administrator    : return 44;
            case self::SeniorOperator   : return 45;
            case self::Operator         : return 42;
            case self::Moderator        : return 43;
            case self::Donator          : return 46;
            case self::Retired          : return 48;
            case self::Trusted          : return 47;
            case self::Member           : throw new Exception('Cannot map Member to Discourse group');
        }
    }

}