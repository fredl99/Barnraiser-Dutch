# Barnraiser-Dutch

## About
This project was published on http://barnraiser.org/ a while ago. Although it has very promising features it was frozen and finally dropped for reasons that are explained [here](http://barnraiser.org/signing_off). According to this the main reason was the rise of similar products by companies with sufficient economic power to attract more users than a small enthusiastic bunch of coders.

Today we know where that evolution led us and there's a tendency to move away from data-miners back to software under personal control. Funny enough this was exactly the intention of all projects at http://barnraiser.org/. We could say they were just a bit too far ahead of their time.

Fortunately the latest versions of the projects are still available on their website. In order to preserve the code and hopefully help to adjust it to modern requirements I took the freedom to import them here. 

I'm not an experienced programmer, so there's not so much activity to be expected from my side. Much more this repository should be considerd as a base-camp for real coders who are able and willing to spend some time on these projects. I don't mind if they fork and develop on their own or ask for write-access to this repo. Although in the first case I would appreciate pull requests in order to keep the things together.

The following is the original description of *Dutch*, taken from its homepage http://barnraiser.org/dutch
There's also a very comprehensive user's manual available at http://barnraiser.org/dutch_guide which I copied
[here](documents/dutch_guide.html) for backup purposes.

## Introducing Dutch
Share information quickly and easily with Dutch; our knowledge sharing network tool.

Dutch is a part of our research to re-think the way we share knowledge on the web. Our goal is to create a fluid pool of knowledge shared amoungst
interested people based upon them working together in gathering information from the web.

## Features
* Install as a single blog or a service to host many separate blogs.
* Lightweight easy to use interface.
* Simple creation of tag based networks.
* Networks favourites listing.
* Notification from your favourite networks.
* Email digest of favourite networks.
* OpenID support.
* Parse Digg items directly into the network.
* Parse Youtube movies into the network.
* Themed "skins" which can be easily downloaded and added.
* Multi-lingual.
* Free (GPL) software license

## Technical considerations
Dutch requires a web server running either Apache 1.3/2.x or IIS5/IIS6 with PHP5.x installed including GD library and Gettext (Curl and BCMath if you
want OpenID support).

For multiple instances you will require access to sub-domains.
