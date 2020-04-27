# COVID-19 in Maryland

This is a PHP script to find cases of COVID-19 by Maryland cities/towns. Automatically updates with the Maryland Department of Health's daily database updates. The Department offers data by ZIP code: this tool organizes them into groups based on a user input of a city/town.

Publicly available data includes regional case counts across the country, for example, as in a repository provided by The New York Times. However, that data usually indexes large metro areas or counties. This offers more specific data with city/town names, but is limited to the scope of Maryland.

If the Maryland Department of Health edits their database schema, this tool may become obsolete. This tool does not provide any new information that the Department does not offer, however, it is often more convenient to examine cases by city/town rather than by ZIP Code.

# Full List of City/Town names recognized by Maryland

* Abell
* Aberdeen
* Aberdeen Proving Ground
* Abingdon
* Accident
* Accokeek
* Adamstown
* Andrews Air Force Base
* Annapolis
* Annapolis Junction
* Aquasco
* Arlington
* Arnold
* Ashton
* Assateague Island
* Avenue
* Baldwin
* Baltimore
* Barclay
* Barnesville
* Barton
* Beallsville
* Bel Air
* Bel Alton
* Belcamp
* Beltsville
* Benedict
* Berlin
* Bethesda
* Betterton
* Big Pool
* Bishopville
* Bittinger
* Bivalve
* Bladensburg
* Bloomington
* Boonsboro
* Bowie
* Boyds
* Bozman
* Brandywine
* Brentwood
* Brinklow
* Brookeville
* Brooklyn
* Broomes Island
* Brunswick
* Bryans Road
* Bryantown
* Burkittsville
* Burtonsville
* Bushwood
* BWI Airport
* Cabin John
* California
* Callaway
* Cambridge
* Capitol Heights
* Carroll
* Cascade
* Catonsville
* Centreville
* Chaptico
* Charlotte Hall
* Cheltenham
* Chesapeake Beach
* Chesapeake City
* Chester
* Chestertown
* Chevy Chase
* Church Creek
* Church Hill
* Churchton
* Churchville
* Clarksburg
* Clarksville
* Clear Spring
* Clements
* Clifton
* Clinton
* Cobb Island
* Cockeysville
* College Park
* Colora
* Coltons Point
* Columbia
* Conowingo
* Cooksville
* Cordova
* Crapo
* Crisfield
* Crocheron
* Crofton
* Crownsville
* Cumberland
* Curtis Bay
* Damascus
* Dameron
* Darlington
* Davidsonville
* Dayton
* Deal Island
* Deale
* Delmar
* Denton
* Derwood
* Dickerson
* District Heights
* Dowell
* Drayden
* Druid
* Dundalk
* Dunkirk
* Earleville
* East New Market
* Easton
* Eastport
* Eden
* Edgewater
* Edgewood
* Elkridge
* Elkton
* Ellicott City
* Emmitsburg
* Essex
* Ewell
* Fairplay
* Fallston
* Faulkner
* Federalsburg
* Finksburg
* Fishing Creek
* Flintstone
* Forest Hill
* Fork
* Fort George G Meade
* Fort Washington
* Franklin
* Frederick
* Freeland
* Friendship
* Friendsville
* Frostburg
* Fruitland
* Fulton
* Gaithersburg
* Galena
* Galesville
* Gambrills
* Germantown
* Gibson Island
* Girdletree
* Glen Arm
* Glen Burnie
* Glen Echo
* Glenelg
* Glenn Dale
* Glenwood
* Glyndon
* Goldsboro
* Govans
* Grantsville
* Grasonville
* Great Mills
* Greenbelt
* Greensboro
* Gunpowder
* Gwynn Oak
* Hagerstown
* Halethorpe
* Hampstead
* Hancock
* Hanover
* Harmans
* Harwood
* Havre De Grace
* Hebron
* Henderson
* Highland
* Highlandtown
* Hollywood
* Hughesville
* Hunt Valley
* Huntingtown
* Hurlock
* Hyattsville
* Hydes
* Ijamsville
* Indian Head
* Ingleside
* Issue
* Jarrettsville
* Jefferson
* Jessup
* Joppa
* Keedysville
* Kennedyville
* Kensington
* Keymar
* Kingsville
* Kitzmiller
* Knoxville
* La Plata
* Lanham
* Laurel
* Leonardtown
* Lexington Park
* Linkwood
* Linthicum Heights
* Little Orleans
* Lonaconing
* Lothian
* Loveville
* Luke
* Lusby
* Lutherville Timonium
* Madison
* Manchester
* Marbury
* Mardela Springs
* Marion Station
* Marriottsville
* Marydel
* Massey
* Maugansville
* McDaniel
* McHenry
* Mechanicsville
* Middle River
* Middletown
* Millersville
* Millington
* Monkton
* Monrovia
* Montgomery Village
* Morrell Park
* Mount Airy
* Mount Rainier
* Mount Savage
* Mt Washington
* Myersville
* Nanjemoy
* Nanticoke
* Naval Academy
* Neavitt
* New Market
* New Windsor
* Newark
* Newburg
* North Beach
* North East
* Northwood
* Nottingham
* Oakland
* Ocean City
* Odenton
* Oldtown
* Olney
* Owings
* Owings Mills
* Oxford
* Oxon Hill
* Park Hall
* Parkton
* Parkville
* Parsonsburg
* Pasadena
* Patuxent River
* Perry Hall
* Perryville
* Phoenix
* Pikesville
* Piney Point
* Pittsville
* Pocomoke City
* Point of Rocks
* Pomfret
* Poolesville
* Port Deposit
* Port Republic
* Port Tobacco
* Potomac
* Preston
* Prince Frederick
* Princess Anne
* Pylesville
* Quantico
* Queen Anne
* Queenstown
* Randallstown
* Raspeburg
* Rawlings
* Reisterstown
* Rhodesdale
* Ridge
* Ridgely
* Rising Sun
* Riva
* Riverdale
* Rock Hall
* Rockville
* Rocky Ridge
* Rohrersville
* Roland Park
* Rosedale
* Royal Oak
* Sabillasville
* Saint Inigoes
* Saint Leonard
* Saint Michaels
* Salisbury
* Sandy Spring
* Savage
* Scotland
* Severn
* Severna Park
* Shady Side
* Sharpsburg
* Sherwood
* Sherwood Forest
* Showell
* Silver Spring
* Smithsburg
* Snow Hill
* Solomons
* Sparks Glencoe
* Sparrows Point
* Spencerville
* Stevenson
* Stevensville
* Still Pond
* Stockton
* Street
* Sudlersville
* Suitland
* Sunderland
* Swanton
* Sykesville
* Takoma Park
* Tall Timbers
* Taneytown
* Taylors Island
* Temple Hills
* Thurmont
* Tilghman
* Toddville
* Towson
* Tracys Landing
* Trappe
* Tuscarora
* Tyaskin
* Union Bridge
* Upper Falls
* Upper Marlboro
* Upperco
* Valley Lee
* Vienna
* Waldorf
* Walkersville
* Warwick
* Welcome
* West Friendship
* West River
* Westernport
* Westminster
* Westover
* Whaleyville
* White Hall
* White Marsh
* White Plains
* Whiteford
* Willards
* Williamsport
* Windsor Mill
* Wingate
* Wittman
* Woodbine
* Woodsboro
* Woodstock
* Woolford
* Worton
* Wye Mills
