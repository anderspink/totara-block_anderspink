# AND0002_Anders_Pink_Totara_Block

# ap-moodle-block

Moodle plugin block for Anders Pink.
For more information, see https://anderspink.com/moodle-plugin.
To download the plugin, see our [latest release](https://github.com/anderspink/totara-block_anderspink/releases/latest) and download block_anderspink.zip.

# Developer Notes

### Totara Permissions

For a user or admin to be able to, there are two permissions that should be assigned to that person. These are:
`block/anderspink:addinstance` and `block/anderspink:myaddinstance`

To update API keys and plugin settings user should be admin and have these permissions `block/anderspink:manageapikeys`
That will allow user to access `/admin/settings.php?section=blocksettinganderspink`.

To add new briefings to block user should have permission `block/anderspink:managebriefings` and be able to edit block settings.

### Before Use

Before using the plugin, you'll have to make sure you have an Anders Pink API key. The block settings will then allow you to select the content that can be displayed within the block.
You can also provide multiple keys to the plugin to enable the use of briefings across multiple Anders Pink Teams.
