# Steps toward instantiating a test system on an internet facing host (on Christian's Data Center)

## Description of the setup

- I have two host, called nginx1 and nginx2 (for redundancy and failover). These two run nginx reverse proxies
  for a set of DNS domains I have registered. The LE services is provided by Christian for me.

- The app servers are regular VMs, which all they expose to the internet is sshd and nothing else.

- Webtraffic is routed from the nginx rev proxies to the app servers on an internal LAN (10/8 network).

- We will get a new VM its name is irrelevant and only used for ssh access. We do not even need to serve
  https as the nginx does that for us and routes simple http to us.

- question at this point: would our okapi in this case no longer accept plaintext auth? This would complicate
  the generation of the signature a LOT and we would perhaps on the internal network also do https, however
  w/ self signed certs then, we can discuss that later.

## Questions

- Do you understand the topology and function distribution?

- Does it make senes?

- Is it applicaple for what we want to accomplish w/ testsystem?
