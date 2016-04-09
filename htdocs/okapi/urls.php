<?php

namespace okapi;

# This is the list of OKAPI views. Regexps are mapped to namespaces.
# Each namespace should expose the Webservice class with a method "call".
# The "call" method should return OkapiResponse, or throw a BadRequest
# exception.

class OkapiUrls
{
    public static $mapping = array(
        '^(services/.*)\.html$' => 'method_doc',
        '^(services/.*)$' => 'method_call',
        '^introduction\.html$' => 'introduction',
        '^signup\.html$' => 'signup',
        '^examples\.html$' => 'examples',
        '^changelog\.html$' => 'changelog',
        '^changelog_feed$' => 'changelog_feed',
        '^$' => 'index',
        '^apps/$' => 'apps/index',
        '^apps/authorize$' => 'apps/authorize',
        '^apps/authorized$' => 'apps/authorized',
        '^apps/revoke_access$' => 'apps/revoke_access',
        '^update/?$' => 'update',
        '^cron5$' => 'cron5',
        '^devel/attrlist$' => 'devel/attrlist',
        '^devel/dbstruct$' => 'devel/dbstruct',
        '^devel/cronreport$' => 'devel/cronreport',
        '^devel/tilereport$' => 'devel/tilereport',
        '^devel/clogentry$' => 'devel/clogentry',
        '^devel/sysinfo$' => 'devel/sysinfo',

        # For debugging TileMap performance only.
        // '^tilestress$' => 'tilestress',
    );
}

# This line is used for commit-hooks testing: ..........>
