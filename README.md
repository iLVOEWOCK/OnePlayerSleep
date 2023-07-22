# OnePlayerSleep

OnePlayerSleep is a plugin for PocketMine-MP that allows a single player to skip the night and set the time to day by sleeping in a bed.

## Features
- If only one player is online and they are sleeping, the time will be set to day.
- If multiple players are online, a sleep task will be started, and when the task completes, the time will be set to day.
- The plugin provides notifications and messages to players during the sleep process.

## Installation
1. Download the latest release of the OnePlayerSleep plugin.
2. Place the `OnePlayerSleep.phar` file in the `plugins` folder of your PocketMine-MP server.
3. Restart the server to load the plugin.

## Usage
- When a player enters a bed, they will start sleeping.
- If only one player is online and sleeping, the time will be set to day.
- If multiple players are online and one player is sleeping, other players will be notified that a player is sleeping.
- After a certain delay, if the player is still sleeping, the time will be set to day.
- If the player wakes up before the delay, they will be notified and the time will remain as it is.

## Commands
There are no commands provided by this plugin.

## Configuration
There are no configuration options for this plugin.

## Note
This is just a silly plugin, i dont intend to fully maintain this as if now. maybe some slight updates here in there

## Contributing
Contributions are welcome! If you have any bug reports, feature requests, or improvements, please create an issue or submit a pull request on the [GitHub repository](https://github.com/iLVOEWOCK/OnePlayerSleep).

## License
This plugin is licensed under the [GNU License](LICENSE).

## To Do
- [x] Make the task a separate file
- [x] Clean up code
- [ ] fix day counter
      
