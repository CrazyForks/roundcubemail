<?php

namespace Roundcube\Plugins\Tests;

use PHPUnit\Framework\TestCase;

use function Roundcube\Tests\invokeMethod;

class VcardAttachmentsTest extends TestCase
{
    /**
     * Plugin object construction test
     */
    public function test_constructor()
    {
        $rcube = \rcube::get_instance();
        $plugin = new \vcard_attachments($rcube->plugins);

        $this->assertInstanceOf('vcard_attachments', $plugin);
        $this->assertInstanceOf('rcube_plugin', $plugin);
    }

    /**
     * Test is_vcard()
     */
    public function test_is_vcard()
    {
        $rcube = \rcube::get_instance();
        $plugin = new \vcard_attachments($rcube->plugins);

        $part = new \rcube_message_part();
        $this->assertFalse(invokeMethod($plugin, 'is_vcard', [$part]));

        $part->mimetype = 'text/vcard';
        $this->assertTrue(invokeMethod($plugin, 'is_vcard', [$part]));

        $part->mimetype = 'text/x-vcard';
        $this->assertTrue(invokeMethod($plugin, 'is_vcard', [$part]));

        $part->mimetype = 'text/directory';
        $this->assertFalse(invokeMethod($plugin, 'is_vcard', [$part]));

        $part->ctype_parameters['profile'] = 'vcard';
        $this->assertTrue(invokeMethod($plugin, 'is_vcard', [$part]));

        $part->ctype_parameters['profile'] = 'unknown';
        $part->filename = 'vcard.vcf';
        $this->assertTrue(invokeMethod($plugin, 'is_vcard', [$part]));
    }
}
