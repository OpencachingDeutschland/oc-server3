<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

// temporarily get rid of update trigger so that we can change last_modified

sql_dropTrigger('cacheLocationBeforeUpdate');
