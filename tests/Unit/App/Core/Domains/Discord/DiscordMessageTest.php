<?php

use App\Core\Domains\Discord\Data\DiscordMessage;

describe('json', function () {
    it('does not contain thread_name if null', function () {
        $message = new DiscordMessage;
        $json = $message->toJson();
        expect($json['thread_name'])->toBeNull();

        $message = new DiscordMessage(threadName: 'test');
        $json = $message->toJson();
        expect($json['thread_name'])->toEqual('test');
    });

    it('truncates thread_name over 100 characters', function () {
        $message = new DiscordMessage(threadName: str_repeat('a', 100));
        $json = $message->toJson();
        expect($json['thread_name'])->toEqual(str_repeat('a', 100));

        $message = new DiscordMessage(threadName: str_repeat('a', 101));
        $json = $message->toJson();
        expect($json['thread_name'])->toEqual(str_repeat('a', 97).'...');
    });
});
