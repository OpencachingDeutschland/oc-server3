


<!DOCTYPE html>
<html>
  <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# githubog: http://ogp.me/ns/fb/githubog#">
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>opencaching/code/htdocs/util2/replication_monitor/replication_monitor.sh at master · OpencachingTeam/opencaching</title>
    <link rel="search" type="application/opensearchdescription+xml" href="/opensearch.xml" title="GitHub" />
    <link rel="fluid-icon" href="https://github.com/fluidicon.png" title="GitHub" />
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-114.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-144.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144.png" />
    <link rel="logo" type="image/svg" href="https://github-media-downloads.s3.amazonaws.com/github-logo.svg" />
    <meta property="og:image" content="https://github.global.ssl.fastly.net/images/modules/logos_page/Octocat.png">
    <meta name="hostname" content="fe2.rs.github.com">
    <link rel="assets" href="https://github.global.ssl.fastly.net/">
    <link rel="xhr-socket" href="/_sockets" />
    
    


    <meta name="msapplication-TileImage" content="/windows-tile.png" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="selected-link" value="repo_source" data-pjax-transient />
    <meta content="collector.githubapp.com" name="octolytics-host" /><meta content="github" name="octolytics-app-id" /><meta content="1614754" name="octolytics-actor-id" /><meta content="following5" name="octolytics-actor-login" /><meta content="0b4acbd8b6317894272d1314424a8efac82d14846f4d4ee3c94acb517aa67425" name="octolytics-actor-hash" />

    
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />

    <meta content="authenticity_token" name="csrf-param" />
<meta content="F+5hJFVmaB3SwkSQOneahOL6SIlkge2FflyW+5qaCdI=" name="csrf-token" />

    <link href="https://github.global.ssl.fastly.net/assets/github-75fcd9a168acc3491e0da5267b38eaac11244b8f.css" media="all" rel="stylesheet" type="text/css" />
    <link href="https://github.global.ssl.fastly.net/assets/github2-cb6181999056f35a857fc48b05b6f4ed11bb140c.css" media="all" rel="stylesheet" type="text/css" />
    


      <script src="https://github.global.ssl.fastly.net/assets/frameworks-e8054ad804a1cf9e9849130fee5a4a5487b663ed.js" type="text/javascript"></script>
      <script src="https://github.global.ssl.fastly.net/assets/github-d7ead42bca9fc0409f5a3ce41a7bac05e9347ab2.js" type="text/javascript"></script>
      
      <meta http-equiv="x-pjax-version" content="187a4452e0ea22b079d562cf557c8f0f">

        <link data-pjax-transient rel='permalink' href='/OpencachingTeam/opencaching/blob/57423c74ca8b23317f553e7e63ac840794518f4b/code/htdocs/util2/replication_monitor/replication_monitor.sh'>
  <meta property="og:title" content="opencaching"/>
  <meta property="og:type" content="githubog:gitrepository"/>
  <meta property="og:url" content="https://github.com/OpencachingTeam/opencaching"/>
  <meta property="og:image" content="https://github.global.ssl.fastly.net/images/gravatars/gravatar-user-420.png"/>
  <meta property="og:site_name" content="GitHub"/>
  <meta property="og:description" content="The source code for the opencaching nodes"/>

  <meta name="description" content="The source code for the opencaching nodes" />

  <meta content="761569" name="octolytics-dimension-user_id" /><meta content="OpencachingTeam" name="octolytics-dimension-user_login" /><meta content="1687072" name="octolytics-dimension-repository_id" /><meta content="OpencachingTeam/opencaching" name="octolytics-dimension-repository_nwo" /><meta content="true" name="octolytics-dimension-repository_public" /><meta content="true" name="octolytics-dimension-repository_is_fork" /><meta content="1226132" name="octolytics-dimension-repository_parent_id" /><meta content="totsubo/se2de-merge" name="octolytics-dimension-repository_parent_nwo" /><meta content="1226132" name="octolytics-dimension-repository_network_root_id" /><meta content="totsubo/se2de-merge" name="octolytics-dimension-repository_network_root_nwo" />
  <link href="https://github.com/OpencachingTeam/opencaching/commits/master.atom" rel="alternate" title="Recent Commits to opencaching:master" type="application/atom+xml" />

  </head>


  <body class="logged_in page-blob windows vis-public fork env-production ">

    <div class="wrapper">
      
      
      


      <div class="header header-logged-in true">
  <div class="container clearfix">

    <a class="header-logo-invertocat" href="https://github.com/">
  <span class="mega-octicon octicon-mark-github"></span>
</a>

    <div class="divider-vertical"></div>

    
  <a href="/notifications" class="notification-indicator tooltipped downwards" title="You have unread notifications">
    <span class="mail-status unread"></span>
  </a>
  <div class="divider-vertical"></div>


      <div class="command-bar js-command-bar  in-repository">
          <form accept-charset="UTF-8" action="/search" class="command-bar-form" id="top_search_form" method="get">

<input type="text" data-hotkey=" s" name="q" id="js-command-bar-field" placeholder="Search or type a command" tabindex="1" autocapitalize="off"
    
    data-username="following5"
      data-repo="OpencachingTeam/opencaching"
      data-branch="master"
      data-sha="a539cb77636e0c1d1e1db75c226d3c7826a4468c"
  >

    <input type="hidden" name="nwo" value="OpencachingTeam/opencaching" />

    <div class="select-menu js-menu-container js-select-menu search-context-select-menu">
      <span class="minibutton select-menu-button js-menu-target">
        <span class="js-select-button">This repository</span>
      </span>

      <div class="select-menu-modal-holder js-menu-content js-navigation-container">
        <div class="select-menu-modal">

          <div class="select-menu-item js-navigation-item js-this-repository-navigation-item selected">
            <span class="select-menu-item-icon octicon octicon-check"></span>
            <input type="radio" class="js-search-this-repository" name="search_target" value="repository" checked="checked" />
            <div class="select-menu-item-text js-select-button-text">This repository</div>
          </div> <!-- /.select-menu-item -->

          <div class="select-menu-item js-navigation-item js-all-repositories-navigation-item">
            <span class="select-menu-item-icon octicon octicon-check"></span>
            <input type="radio" name="search_target" value="global" />
            <div class="select-menu-item-text js-select-button-text">All repositories</div>
          </div> <!-- /.select-menu-item -->

        </div>
      </div>
    </div>

  <span class="octicon help tooltipped downwards" title="Show command bar help">
    <span class="octicon octicon-question"></span>
  </span>


  <input type="hidden" name="ref" value="cmdform">

</form>
        <ul class="top-nav">
            <li class="explore"><a href="/explore">Explore</a></li>
            <li><a href="https://gist.github.com">Gist</a></li>
            <li><a href="/blog">Blog</a></li>
          <li><a href="https://help.github.com">Help</a></li>
        </ul>
      </div>

    

  

    <ul id="user-links">
      <li>
        <a href="/following5" class="name">
          <img height="20" src="https://secure.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e?s=140&amp;d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png" width="20" /> following5
        </a>
      </li>

        <li>
          <a href="/new" id="new_repo" class="tooltipped downwards" title="Create a new repo" aria-label="Create a new repo">
            <span class="octicon octicon-repo-create"></span>
          </a>
        </li>

        <li>
          <a href="/settings/profile" id="account_settings"
            class="tooltipped downwards"
            aria-label="Account settings "
            title="Account settings ">
            <span class="octicon octicon-tools"></span>
          </a>
        </li>
        <li>
          <a class="tooltipped downwards" href="/logout" data-method="post" id="logout" title="Sign out" aria-label="Sign out">
            <span class="octicon octicon-log-out"></span>
          </a>
        </li>

    </ul>


<div class="js-new-dropdown-contents hidden">
  

<ul class="dropdown-menu">
  <li>
    <a href="/new"><span class="octicon octicon-repo-create"></span> New repository</a>
  </li>
  <li>
    <a href="/organizations/new"><span class="octicon octicon-organization"></span> New organization</a>
  </li>



    <li class="section-title">
      <span title="OpencachingTeam/opencaching">This repository</span>
    </li>
    <li>
      <a href="/OpencachingTeam/opencaching/issues/new"><span class="octicon octicon-issue-opened"></span> New issue</a>
    </li>
</ul>

</div>


    
  </div>
</div>

      

      




          <div class="site" itemscope itemtype="http://schema.org/WebPage">
    
    <div class="pagehead repohead instapaper_ignore readability-menu">
      <div class="container">
        

<ul class="pagehead-actions">

    <li class="subscription">
      <form accept-charset="UTF-8" action="/notifications/subscribe" class="js-social-container" data-autosubmit="true" data-remote="true" method="post"><div style="margin:0;padding:0;display:inline"><input name="authenticity_token" type="hidden" value="F+5hJFVmaB3SwkSQOneahOL6SIlkge2FflyW+5qaCdI=" /></div>  <input id="repository_id" name="repository_id" type="hidden" value="1687072" />

    <div class="select-menu js-menu-container js-select-menu">
        <a class="social-count js-social-count" href="/OpencachingTeam/opencaching/watchers">
          3
        </a>
      <span class="minibutton select-menu-button with-count js-menu-target">
        <span class="js-select-button">
          <span class="octicon octicon-eye-watch"></span>
          Watch
        </span>
      </span>

      <div class="select-menu-modal-holder">
        <div class="select-menu-modal subscription-menu-modal js-menu-content">
          <div class="select-menu-header">
            <span class="select-menu-title">Notification status</span>
            <span class="octicon octicon-remove-close js-menu-close"></span>
          </div> <!-- /.select-menu-header -->

          <div class="select-menu-list js-navigation-container">

            <div class="select-menu-item js-navigation-item selected">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <div class="select-menu-item-text">
                <input checked="checked" id="do_included" name="do" type="radio" value="included" />
                <h4>Not watching</h4>
                <span class="description">You only receive notifications for discussions in which you participate or are @mentioned.</span>
                <span class="js-select-button-text hidden-select-button-text">
                  <span class="octicon octicon-eye-watch"></span>
                  Watch
                </span>
              </div>
            </div> <!-- /.select-menu-item -->

            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon octicon-check"></span>
              <div class="select-menu-item-text">
                <input id="do_subscribed" name="do" type="radio" value="subscribed" />
                <h4>Watching</h4>
                <span class="description">You receive notifications for all discussions in this repository.</span>
                <span class="js-select-button-text hidden-select-button-text">
                  <span class="octicon octicon-eye-unwatch"></span>
                  Unwatch
                </span>
              </div>
            </div> <!-- /.select-menu-item -->

            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <div class="select-menu-item-text">
                <input id="do_ignore" name="do" type="radio" value="ignore" />
                <h4>Ignoring</h4>
                <span class="description">You do not receive any notifications for discussions in this repository.</span>
                <span class="js-select-button-text hidden-select-button-text">
                  <span class="octicon octicon-mute"></span>
                  Stop ignoring
                </span>
              </div>
            </div> <!-- /.select-menu-item -->

          </div> <!-- /.select-menu-list -->

        </div> <!-- /.select-menu-modal -->
      </div> <!-- /.select-menu-modal-holder -->
    </div> <!-- /.select-menu -->

</form>
    </li>

  <li>
  
<div class="js-toggler-container js-social-container starring-container ">
  <a href="/OpencachingTeam/opencaching/unstar" class="minibutton with-count js-toggler-target star-button starred upwards" title="Unstar this repo" data-remote="true" data-method="post" rel="nofollow">
    <span class="octicon octicon-star-delete"></span><span class="text">Unstar</span>
  </a>
  <a href="/OpencachingTeam/opencaching/star" class="minibutton with-count js-toggler-target star-button unstarred upwards " title="Star this repo" data-remote="true" data-method="post" rel="nofollow">
    <span class="octicon octicon-star"></span><span class="text">Star</span>
  </a>
  <a class="social-count js-social-count" href="/OpencachingTeam/opencaching/stargazers">8</a>
</div>

  </li>


        <li>
          <a href="/OpencachingTeam/opencaching/fork" class="minibutton with-count js-toggler-target fork-button lighter upwards" title="Fork this repo" rel="facebox nofollow">
            <span class="octicon octicon-git-branch-create"></span><span class="text">Fork</span>
          </a>
          <a href="/OpencachingTeam/opencaching/network" class="social-count">6</a>
        </li>


</ul>

        <h1 itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="entry-title public">
          <span class="repo-label"><span>public</span></span>
          <span class="mega-octicon octicon-repo-forked"></span>
          <span class="author">
            <a href="/OpencachingTeam" class="url fn" itemprop="url" rel="author"><span itemprop="title">OpencachingTeam</span></a></span
          ><span class="repohead-name-divider">/</span><strong
          ><a href="/OpencachingTeam/opencaching" class="js-current-repository js-repo-home-link">opencaching</a></strong>

          <span class="page-context-loader">
            <img alt="Octocat-spinner-32" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
          </span>

            <span class="fork-flag">
              <span class="text">forked from <a href="/totsubo/se2de-merge">totsubo/se2de-merge</a></span>
            </span>
        </h1>
      </div><!-- /.container -->
    </div><!-- /.repohead -->

    <div class="container">

      <div class="repository-with-sidebar repo-container
            ">

          <div class="repository-sidebar">

              

<div class="repo-nav repo-nav-full js-repository-container-pjax js-octicon-loaders">
  <div class="repo-nav-contents">
    <ul class="repo-menu">
      <li class="tooltipped leftwards" title="Code">
        <a href="/OpencachingTeam/opencaching" aria-label="Code" class="js-selected-navigation-item selected" data-gotokey="c" data-pjax="true" data-selected-links="repo_source repo_downloads repo_commits repo_tags repo_branches /OpencachingTeam/opencaching">
          <span class="octicon octicon-code"></span> <span class="full-word">Code</span>
          <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>      </li>

        <li class="tooltipped leftwards" title="Issues">
          <a href="/OpencachingTeam/opencaching/issues" aria-label="Issues" class="js-selected-navigation-item js-disable-pjax" data-gotokey="i" data-selected-links="repo_issues /OpencachingTeam/opencaching/issues">
            <span class="octicon octicon-issue-opened"></span> <span class="full-word">Issues</span>
            <span class='counter'>2</span>
            <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>        </li>

      <li class="tooltipped leftwards" title="Pull Requests"><a href="/OpencachingTeam/opencaching/pulls" aria-label="Pull Requests" class="js-selected-navigation-item js-disable-pjax" data-gotokey="p" data-selected-links="repo_pulls /OpencachingTeam/opencaching/pulls">
            <span class="octicon octicon-git-pull-request"></span> <span class="full-word">Pull Requests</span>
            <span class='counter'>0</span>
            <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>      </li>


        <li class="tooltipped leftwards" title="Wiki">
          <a href="/OpencachingTeam/opencaching/wiki" aria-label="Wiki" class="js-selected-navigation-item " data-pjax="true" data-selected-links="repo_wiki /OpencachingTeam/opencaching/wiki">
            <span class="octicon octicon-book"></span> <span class="full-word">Wiki</span>
            <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>        </li>


    </ul>
    <div class="repo-menu-separator"></div>
    <ul class="repo-menu">

      <li class="tooltipped leftwards" title="Pulse">
        <a href="/OpencachingTeam/opencaching/pulse" aria-label="Pulse" class="js-selected-navigation-item " data-pjax="true" data-selected-links="pulse /OpencachingTeam/opencaching/pulse">
          <span class="octicon octicon-pulse"></span> <span class="full-word">Pulse</span>
          <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>      </li>

      <li class="tooltipped leftwards" title="Graphs">
        <a href="/OpencachingTeam/opencaching/graphs" aria-label="Graphs" class="js-selected-navigation-item " data-pjax="true" data-selected-links="repo_graphs repo_contributors /OpencachingTeam/opencaching/graphs">
          <span class="octicon octicon-graph"></span> <span class="full-word">Graphs</span>
          <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>      </li>

      <li class="tooltipped leftwards" title="Network">
        <a href="/OpencachingTeam/opencaching/network" aria-label="Network" class="js-selected-navigation-item js-disable-pjax" data-selected-links="repo_network /OpencachingTeam/opencaching/network">
          <span class="octicon octicon-git-branch"></span> <span class="full-word">Network</span>
          <img alt="Octocat-spinner-32" class="mini-loader" height="16" src="https://github.global.ssl.fastly.net/images/spinners/octocat-spinner-32.gif" width="16" />
</a>      </li>

    </ul>

  </div>
</div>


              <div class="only-with-full-nav">

                

  

<div class="clone-url open"
  data-protocol-type="http"
  data-url="/users/set_protocol?protocol_selector=http&amp;protocol_type=clone">
  <h3><strong>HTTPS</strong> clone URL</h3>

  <input type="text" class="clone js-url-field"
         value="https://github.com/OpencachingTeam/opencaching.git" readonly="readonly">

  <span class="js-zeroclipboard url-box-clippy minibutton zeroclipboard-button" data-clipboard-text="https://github.com/OpencachingTeam/opencaching.git" data-copied-hint="copied!" title="copy to clipboard"><span class="octicon octicon-clippy"></span></span>
</div>

  

<div class="clone-url "
  data-protocol-type="ssh"
  data-url="/users/set_protocol?protocol_selector=ssh&amp;protocol_type=clone">
  <h3><strong>SSH</strong> clone URL</h3>

  <input type="text" class="clone js-url-field"
         value="git@github.com:OpencachingTeam/opencaching.git" readonly="readonly">

  <span class="js-zeroclipboard url-box-clippy minibutton zeroclipboard-button" data-clipboard-text="git@github.com:OpencachingTeam/opencaching.git" data-copied-hint="copied!" title="copy to clipboard"><span class="octicon octicon-clippy"></span></span>
</div>

  

<div class="clone-url "
  data-protocol-type="subversion"
  data-url="/users/set_protocol?protocol_selector=subversion&amp;protocol_type=clone">
  <h3><strong>Subversion</strong> checkout URL</h3>

  <input type="text" class="clone js-url-field"
         value="https://github.com/OpencachingTeam/opencaching" readonly="readonly">

  <span class="js-zeroclipboard url-box-clippy minibutton zeroclipboard-button" data-clipboard-text="https://github.com/OpencachingTeam/opencaching" data-copied-hint="copied!" title="copy to clipboard"><span class="octicon octicon-clippy"></span></span>
</div>



<p class="clone-options">You can clone with
    <a href="#" class="js-clone-selector" data-protocol="http">HTTPS</a>,
    <a href="#" class="js-clone-selector" data-protocol="ssh">SSH</a>,
    <a href="#" class="js-clone-selector" data-protocol="subversion">Subversion</a>,
  and <a href="https://help.github.com/articles/which-remote-url-should-i-use">other methods.</a>
</p>


  <a href="http://windows.github.com" class="minibutton sidebar-button">
    <span class="octicon octicon-device-desktop"></span>
    Clone in Desktop
  </a>


                  <a href="/OpencachingTeam/opencaching/archive/master.zip"
                     class="minibutton sidebar-button"
                     title="Download this repository as a zip file"
                     rel="nofollow">
                    <span class="octicon octicon-cloud-download"></span>
                    Download ZIP
                  </a>

              </div>
          </div>

          <div id="js-repo-pjax-container" class="repository-content context-loader-container" data-pjax-container>
            


<!-- blob contrib key: blob_contributors:v21:564950e0aa04adc5258a6960294e171c -->
<!-- blob contrib frag key: views10/v8/blob_contributors:v21:564950e0aa04adc5258a6960294e171c -->

<p title="This is a placeholder element" class="js-history-link-replace hidden"></p>

<a href="/OpencachingTeam/opencaching/find/master" data-pjax data-hotkey="t" style="display:none">Show File Finder</a>

<div class="file-navigation">
  


<div class="select-menu js-menu-container js-select-menu" >
  <span class="minibutton select-menu-button js-menu-target" data-hotkey="w"
    data-master-branch="master"
    data-ref="master">
    <span class="octicon octicon-git-branch"></span>
    <i>branch:</i>
    <span class="js-select-button">master</span>
  </span>

  <div class="select-menu-modal-holder js-menu-content js-navigation-container" data-pjax>

    <div class="select-menu-modal">
      <div class="select-menu-header">
        <span class="select-menu-title">Switch branches/tags</span>
        <span class="octicon octicon-remove-close js-menu-close"></span>
      </div> <!-- /.select-menu-header -->

      <div class="select-menu-filters">
        <div class="select-menu-text-filter">
          <input type="text" id="context-commitish-filter-field" class="js-filterable-field js-navigation-enable" placeholder="Filter branches/tags">
        </div>
        <div class="select-menu-tabs">
          <ul>
            <li class="select-menu-tab">
              <a href="#" data-tab-filter="branches" class="js-select-menu-tab">Branches</a>
            </li>
            <li class="select-menu-tab">
              <a href="#" data-tab-filter="tags" class="js-select-menu-tab">Tags</a>
            </li>
          </ul>
        </div><!-- /.select-menu-tabs -->
      </div><!-- /.select-menu-filters -->

      <div class="select-menu-list select-menu-tab-bucket js-select-menu-tab-bucket" data-tab-filter="branches">

        <div data-filterable-for="context-commitish-filter-field" data-filterable-type="substring">


            <div class="select-menu-item js-navigation-item selected">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/master/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="master" rel="nofollow" title="master">master</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/ocde_updates/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="ocde_updates" rel="nofollow" title="ocde_updates">ocde_updates</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/ocdegpx/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="ocdegpx" rel="nofollow" title="ocdegpx">ocdegpx</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/ocdetemplates/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="ocdetemplates" rel="nofollow" title="ocdetemplates">ocdetemplates</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/ocdetest/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="ocdetest" rel="nofollow" title="ocdetest">ocdetest</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/ocsedev/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="ocsedev" rel="nofollow" title="ocsedev">ocsedev</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/ocseprod/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="ocseprod" rel="nofollow" title="ocseprod">ocseprod</a>
            </div> <!-- /.select-menu-item -->
            <div class="select-menu-item js-navigation-item ">
              <span class="select-menu-item-icon octicon octicon-check"></span>
              <a href="/OpencachingTeam/opencaching/blob/rootpath/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="js-navigation-open select-menu-item-text js-select-button-text css-truncate-target" data-name="rootpath" rel="nofollow" title="rootpath">rootpath</a>
            </div> <!-- /.select-menu-item -->
        </div>

          <div class="select-menu-no-results">Nothing to show</div>
      </div> <!-- /.select-menu-list -->

      <div class="select-menu-list select-menu-tab-bucket js-select-menu-tab-bucket" data-tab-filter="tags">
        <div data-filterable-for="context-commitish-filter-field" data-filterable-type="substring">


        </div>

        <div class="select-menu-no-results">Nothing to show</div>
      </div> <!-- /.select-menu-list -->

    </div> <!-- /.select-menu-modal -->
  </div> <!-- /.select-menu-modal-holder -->
</div> <!-- /.select-menu -->

  <div class="breadcrumb">
    <span class='repo-root js-repo-root'><span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/OpencachingTeam/opencaching" data-branch="master" data-direction="back" data-pjax="true" itemscope="url"><span itemprop="title">opencaching</span></a></span></span><span class="separator"> / </span><span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/OpencachingTeam/opencaching/tree/master/code" data-branch="master" data-direction="back" data-pjax="true" itemscope="url"><span itemprop="title">code</span></a></span><span class="separator"> / </span><span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/OpencachingTeam/opencaching/tree/master/code/htdocs" data-branch="master" data-direction="back" data-pjax="true" itemscope="url"><span itemprop="title">htdocs</span></a></span><span class="separator"> / </span><span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/OpencachingTeam/opencaching/tree/master/code/htdocs/util2" data-branch="master" data-direction="back" data-pjax="true" itemscope="url"><span itemprop="title">util2</span></a></span><span class="separator"> / </span><span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/OpencachingTeam/opencaching/tree/master/code/htdocs/util2/replication_monitor" data-branch="master" data-direction="back" data-pjax="true" itemscope="url"><span itemprop="title">replication_monitor</span></a></span><span class="separator"> / </span><strong class="final-path">replication_monitor.sh</strong> <span class="js-zeroclipboard minibutton zeroclipboard-button" data-clipboard-text="code/htdocs/util2/replication_monitor/replication_monitor.sh" data-copied-hint="copied!" title="copy to clipboard"><span class="octicon octicon-clippy"></span></span>
  </div>
</div>


  
  <div class="commit file-history-tease">
    <img class="main-avatar" height="24" src="https://secure.gravatar.com/avatar/b3609286d56a88ce59bfec29a5aae9c2?s=140&amp;d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png" width="24" />
    <span class="author"><a href="/totsubo" rel="author">totsubo</a></span>
    <time class="js-relative-date" datetime="2011-01-06T03:44:30-08:00" title="2011-01-06 03:44:30">January 06, 2011</time>
    <div class="commit-title">
        <a href="/OpencachingTeam/opencaching/commit/74432eb287a70279c4f601ee9345e971b099dd9f" class="message" data-pjax="true">Initial import</a>
    </div>

    <div class="participation">
      <p class="quickstat"><a href="#blob_contributors_box" rel="facebox"><strong>1</strong> contributor</a></p>
      
    </div>
    <div id="blob_contributors_box" style="display:none">
      <h2 class="facebox-header">Users who have contributed to this file</h2>
      <ul class="facebox-user-list">
        <li class="facebox-user-list-item">
          <img height="24" src="https://secure.gravatar.com/avatar/b3609286d56a88ce59bfec29a5aae9c2?s=140&amp;d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png" width="24" />
          <a href="/totsubo">totsubo</a>
        </li>
      </ul>
    </div>
  </div>


<div id="files" class="bubble">
  <div class="file">
    <div class="meta">
      <div class="info">
        <span class="icon"><b class="octicon octicon-file-text"></b></span>
        <span class="mode" title="File Mode">file</span>
          <span>45 lines (40 sloc)</span>
        <span>1.248 kb</span>
      </div>
      <div class="actions">
        <div class="button-group">
                <a class="minibutton tooltipped leftwards"
                   title="Clicking this button will automatically fork this project so you can edit the file"
                   href="/OpencachingTeam/opencaching/edit/master/code/htdocs/util2/replication_monitor/replication_monitor.sh"
                   data-method="post" rel="nofollow">Edit</a>
          <a href="/OpencachingTeam/opencaching/raw/master/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="button minibutton " id="raw-url">Raw</a>
            <a href="/OpencachingTeam/opencaching/blame/master/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="button minibutton ">Blame</a>
          <a href="/OpencachingTeam/opencaching/commits/master/code/htdocs/util2/replication_monitor/replication_monitor.sh" class="button minibutton " rel="nofollow">History</a>
        </div><!-- /.button-group -->
            <a class="minibutton danger empty-icon tooltipped downwards"
               href="/OpencachingTeam/opencaching/delete/master/code/htdocs/util2/replication_monitor/replication_monitor.sh"
               title="Fork this project and delete file" data-method="post" rel="nofollow">
            Delete
          </a>
      </div><!-- /.actions -->

    </div>
        <div class="blob-wrapper data type-shell js-blob-data">
      <table class="file-code file-diff">
        <tr class="file-code-line">
          <td class="blob-line-nums">
            <span id="L1" rel="#L1">1</span>
<span id="L2" rel="#L2">2</span>
<span id="L3" rel="#L3">3</span>
<span id="L4" rel="#L4">4</span>
<span id="L5" rel="#L5">5</span>
<span id="L6" rel="#L6">6</span>
<span id="L7" rel="#L7">7</span>
<span id="L8" rel="#L8">8</span>
<span id="L9" rel="#L9">9</span>
<span id="L10" rel="#L10">10</span>
<span id="L11" rel="#L11">11</span>
<span id="L12" rel="#L12">12</span>
<span id="L13" rel="#L13">13</span>
<span id="L14" rel="#L14">14</span>
<span id="L15" rel="#L15">15</span>
<span id="L16" rel="#L16">16</span>
<span id="L17" rel="#L17">17</span>
<span id="L18" rel="#L18">18</span>
<span id="L19" rel="#L19">19</span>
<span id="L20" rel="#L20">20</span>
<span id="L21" rel="#L21">21</span>
<span id="L22" rel="#L22">22</span>
<span id="L23" rel="#L23">23</span>
<span id="L24" rel="#L24">24</span>
<span id="L25" rel="#L25">25</span>
<span id="L26" rel="#L26">26</span>
<span id="L27" rel="#L27">27</span>
<span id="L28" rel="#L28">28</span>
<span id="L29" rel="#L29">29</span>
<span id="L30" rel="#L30">30</span>
<span id="L31" rel="#L31">31</span>
<span id="L32" rel="#L32">32</span>
<span id="L33" rel="#L33">33</span>
<span id="L34" rel="#L34">34</span>
<span id="L35" rel="#L35">35</span>
<span id="L36" rel="#L36">36</span>
<span id="L37" rel="#L37">37</span>
<span id="L38" rel="#L38">38</span>
<span id="L39" rel="#L39">39</span>
<span id="L40" rel="#L40">40</span>
<span id="L41" rel="#L41">41</span>
<span id="L42" rel="#L42">42</span>
<span id="L43" rel="#L43">43</span>
<span id="L44" rel="#L44">44</span>

          </td>
          <td class="blob-line-code">
                  <div class="highlight"><pre><div class='line' id='LC1'><span class="c">#!/bin/bash</span></div><div class='line' id='LC2'><span class="c">#</span></div><div class='line' id='LC3'><span class="c">#   Opencaching replication monitor bash script</span></div><div class='line' id='LC4'><span class="c">#</span></div><div class='line' id='LC5'><span class="c"># This script writes every 10 seconds the current timestamp to</span></div><div class='line' id='LC6'><span class="c"># table sys_repl_timestamp. This enables the cron-module </span></div><div class='line' id='LC7'><span class="c"># repliaction_monitor to check if the mysql replication slave(s) is up to </span></div><div class='line' id='LC8'><span class="c"># date and online.</span></div><div class='line' id='LC9'><span class="c">#</span></div><div class='line' id='LC10'><span class="c"># You should place this bash script outside the PHP configured </span></div><div class='line' id='LC11'><span class="c"># open_basedir restriction and place a cronjob entry that</span></div><div class='line' id='LC12'><span class="c"># executes every 5 minutes or place it in rc.3 or rc.5</span></div><div class='line' id='LC13'><span class="c"># (run this script on the master database server, not on any slave!)</span></div><div class='line' id='LC14'><span class="c">#</span></div><div class='line' id='LC15'><span class="c"># If you setup a cronjob call it with parameter &quot;-q&quot; to prevent</span></div><div class='line' id='LC16'><span class="c"># output of running-message.</span></div><div class='line' id='LC17'><span class="c">#</span></div><div class='line' id='LC18'><br/></div><div class='line' id='LC19'><span class="c"># begin of configuration</span></div><div class='line' id='LC20'><span class="nv">PIDFILE</span><span class="o">=</span>/var/run/oc_replication_monitor.pid</div><div class='line' id='LC21'><span class="nv">DBHOST</span><span class="o">=</span>oc</div><div class='line' id='LC22'><span class="nv">DBNAME</span><span class="o">=</span>oc</div><div class='line' id='LC23'><span class="nv">DBUSER</span><span class="o">=</span>oc</div><div class='line' id='LC24'><span class="nv">DBPASSWORD</span><span class="o">=</span>oc</div><div class='line' id='LC25'><span class="c"># end of configuration</span></div><div class='line' id='LC26'><br/></div><div class='line' id='LC27'><span class="k">if</span> <span class="o">[</span> -f <span class="nv">$PIDFILE</span> <span class="o">]</span>; <span class="k">then</span></div><div class='line' id='LC28'><span class="k">  if</span> <span class="o">[</span> -d /proc/<span class="sb">`</span>cat <span class="nv">$PIDFILE</span><span class="sb">`</span> <span class="o">]</span>; <span class="k">then</span></div><div class='line' id='LC29'><span class="k">    if</span> <span class="o">(</span>readlink /proc/<span class="sb">`</span>cat <span class="nv">$PIDFILE</span><span class="sb">`</span>/exe | grep -q /bin/bash<span class="o">)</span>; <span class="k">then</span></div><div class='line' id='LC30'><span class="k">      if</span> <span class="o">[</span> <span class="s2">&quot;$1&quot;</span> !<span class="o">=</span> <span class="s2">&quot;-q&quot;</span> <span class="o">]</span>; <span class="k">then</span></div><div class='line' id='LC31'><span class="k">        </span><span class="nb">echo</span> <span class="s2">&quot;replication_monitor running with pid `cat $PIDFILE`, exiting&quot;</span></div><div class='line' id='LC32'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="k">fi</span></div><div class='line' id='LC33'><span class="k">      </span><span class="nb">exit</span></div><div class='line' id='LC34'><span class="nb">    </span><span class="k">fi</span></div><div class='line' id='LC35'><span class="k">  fi</span></div><div class='line' id='LC36'><span class="k">fi</span></div><div class='line' id='LC37'><br/></div><div class='line' id='LC38'><span class="nb">echo</span> <span class="nv">$$</span> &gt; <span class="nv">$PIDFILE</span></div><div class='line' id='LC39'><br/></div><div class='line' id='LC40'><span class="k">while</span> <span class="o">[</span> 1 <span class="o">]</span></div><div class='line' id='LC41'><span class="k">do</span></div><div class='line' id='LC42'><span class="k">  </span>mysql -h<span class="nv">$DBHOST</span> -u<span class="nv">$DBUSER</span> -p<span class="nv">$DBPASSWORD</span> <span class="nv">$DBNAME</span> --execute<span class="o">=</span><span class="s2">&quot;INSERT INTO sys_repl_timestamp (id, data) VALUES (1, NOW()) ON DUPLICATE KEY UPDATE data=NOW();&quot;</span></div><div class='line' id='LC43'>&nbsp;&nbsp;sleep 10</div><div class='line' id='LC44'><span class="k">done</span></div></pre></div>
          </td>
        </tr>
      </table>
  </div>

  </div>
</div>

<a href="#jump-to-line" rel="facebox[.linejump]" data-hotkey="l" class="js-jump-to-line" style="display:none">Jump to Line</a>
<div id="jump-to-line" style="display:none">
  <form accept-charset="UTF-8" class="js-jump-to-line-form">
    <input class="linejump-input js-jump-to-line-field" type="text" placeholder="Jump to line&hellip;" autofocus>
    <button type="submit" class="button">Go</button>
  </form>
</div>

          </div>
        </div>

      </div><!-- /.repo-container -->
      <div class="modal-backdrop"></div>
    </div>
  </div><!-- /.site -->


    </div><!-- /.wrapper -->

      <div class="container">
  <div class="site-footer">
    <ul class="site-footer-links right">
      <li><a href="https://status.github.com/">Status</a></li>
      <li><a href="http://developer.github.com">API</a></li>
      <li><a href="http://training.github.com">Training</a></li>
      <li><a href="http://shop.github.com">Shop</a></li>
      <li><a href="/blog">Blog</a></li>
      <li><a href="/about">About</a></li>

    </ul>

    <a href="/">
      <span class="mega-octicon octicon-mark-github"></span>
    </a>

    <ul class="site-footer-links">
      <li>&copy; 2013 <span title="0.11380s from fe2.rs.github.com">GitHub</span>, Inc.</li>
        <li><a href="/site/terms">Terms</a></li>
        <li><a href="/site/privacy">Privacy</a></li>
        <li><a href="/security">Security</a></li>
        <li><a href="/contact">Contact</a></li>
    </ul>
  </div><!-- /.site-footer -->
</div><!-- /.container -->


    <div class="fullscreen-overlay js-fullscreen-overlay" id="fullscreen_overlay">
  <div class="fullscreen-container js-fullscreen-container">
    <div class="textarea-wrap">
      <textarea name="fullscreen-contents" id="fullscreen-contents" class="js-fullscreen-contents" placeholder="" data-suggester="fullscreen_suggester"></textarea>
          <div class="suggester-container">
              <div class="suggester fullscreen-suggester js-navigation-container" id="fullscreen_suggester"
                 data-url="/OpencachingTeam/opencaching/suggestions/commit">
              </div>
          </div>
    </div>
  </div>
  <div class="fullscreen-sidebar">
    <a href="#" class="exit-fullscreen js-exit-fullscreen tooltipped leftwards" title="Exit Zen Mode">
      <span class="mega-octicon octicon-screen-normal"></span>
    </a>
    <a href="#" class="theme-switcher js-theme-switcher tooltipped leftwards"
      title="Switch themes">
      <span class="octicon octicon-color-mode"></span>
    </a>
  </div>
</div>



    <div id="ajax-error-message" class="flash flash-error">
      <span class="octicon octicon-alert"></span>
      <a href="#" class="octicon octicon-remove-close close ajax-error-dismiss"></a>
      Something went wrong with that request. Please try again.
    </div>

    
  </body>
</html>

