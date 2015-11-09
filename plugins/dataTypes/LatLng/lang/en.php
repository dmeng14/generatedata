<?php

$L = array();
$L["DATA_TYPE_NAME"] = "Latitude / Longitude";

$L["latitude"] = "Latitude";
$L["longitude"] = "Longitude";
$L["latbase"] = "Base";
$L["latdist"] = "Dist";
$L["lngbase"] = "Base";
$L["lngdist"] = "Dist";
$L["latlngmax"] = "options: MAX";
$L["latlngstd"] = "STD";
$L["circular"] = "CircularDist";
$L["cir_center"] = "Center: ";
$L["lat"] = "Lat";
$L["lng"] = "Lng";
$L["help"] = "This data type generates a latitude and/or longitude. If both are selected, it displays both separated by a comma. Base is a geo-decimal latitude/longitude value in degree, within the range of -90 to 90 for latitude, and -180 to 180 for longitude. Dist is a real distance value in miles. Dist will be converted to the corresponding geo-decimal offset d' in degree. The MAX option will generate data by using uniform distribution U(base - d', base + d'), and STD option will generate data by using normal distribution N(base, d'). Extra feature of generating latitude/longitude point value according to the circular distance of a center is added. To avoid confusion, please use choose either linear distance option or circular distance option.";
$L["incomplete_fields"] = "Please enter a number for the following rows: ";