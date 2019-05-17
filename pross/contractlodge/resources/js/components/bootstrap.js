
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Spark components which makes up the core client
 | application. This is also a convenient spot for you to load all of
 | your components that you write while building your applications.
 */

require('./../spark-components/bootstrap');

require('./home');

require('./races');
require('./races/edit');
require('./races/show');
require('./races/create');

require('./clients');
require('./clients/edit');
require('./clients/show');
require('./clients/create');
require('./clients/create-custom');

require('./invoices');
require('./invoices/edit');
require('./invoices/show');
require('./invoices/create');

require('./hotels');
require('./hotels/edit');
require('./hotels/show');
require('./hotels/create');
require('./hotels/search');
require('./hotels/reconcile');

require('./bills');
require('./bills/edit');

require('./reservations');

require('./settings/profile/update-notification-options');

require('./reports/reports');
require('./reports/confirmation');
