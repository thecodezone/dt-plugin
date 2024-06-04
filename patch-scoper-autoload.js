import fs from 'fs';
import path from 'path';

// Define our find and replace strings.
let STRINGABLE = "humbug_phpscoper_expose_class('Stringable', 'CodeZone\\Bible\\Stringable');";
let PHPTOKEN = "humbug_phpscoper_expose_class('PhpToken', 'CodeZone\\Bible\\PhpToken');";
let FILE = path.join(process.cwd(), 'vendor-scoped/scoper-autoload.php');  // adjust the path

// Read our file content in 'utf-8'.
let fileContent = fs.readFileSync(FILE, 'utf-8');

// Check if the "Stringable" line is before the "PhpToken" line.
if (fileContent.indexOf(STRINGABLE) > fileContent.indexOf(PHPTOKEN)) {
    // If it isn't, we replace the "Stringable" line with "" (effectively removing the line).
    fileContent = fileContent.replace(STRINGABLE, '');
    // Then we replace the "PhpToken" line with "Stringable" + "PhpToken".
    fileContent = fileContent.replace(PHPTOKEN, STRINGABLE + '\n' + PHPTOKEN);
}

fs.writeFileSync(FILE, fileContent, 'utf-8');
