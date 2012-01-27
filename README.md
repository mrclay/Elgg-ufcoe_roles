
Keep this plugin at the top and with the folder named "ufcoe_roles".

Do not load your class until your init() function, as the UFCOE_RoleProvider won't be loaded until the init stage.

Add your providers in your init(), but ideally don't try to fetch roles until after the system/init phase has ended (to make sure other plugins have had a change to add their providers).

The Elgg provider is automatically added, so admins will get the "elgg:admin" role.