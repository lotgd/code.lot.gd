# Legend of the Green Dragon Code Repository

http://code.lot.gd is a collection of modules to extend the
Legend of the Green Dragon, Daenerys edition (see http://github.com/lotgd/core),
based on the Composer package management system.

## How do I install a module?

In your `composer.json`, first add code.lot.gd as a repository so Composer knows
where to look for LotGD packages. Then add your chosen module as a dependency as
you normally would any Composer package..

Your `composer.json` then might look something like this, if you wanted to
install the Hello World module (http://github.com/lotgd/module-helloworld):

```
{
  ...
  "repositories": [
    {
      "type": "composer",
      "url": "http://code.lot.gd/"
    }
  ],
  "require": {
    "lotgd/module-helloworld": "*"
  }
  ...
}
```

## How do I create my own module and make it available on code.lot.gd?

Make your own module by creating a composer package in a github repo. Be sure the
type is `lotgd-module`. More documentation on this process will be produced when the
LotGD core is more mature.

Then, put up a pull request in this repo to add your package to the list in `satis.json`.
Follow the example of the Hello World module:

```
  "repositories": [
    { "type": "vcs", "url": "https://github.com/lotgd/module-helloworld" },
    { "type": "vcs", "url": "https://github.com/your/module-url-here" }
  ]
```

## Deployment

Create a docker image:
```
docker build --build-arg GITHUB_TOKEN=<token> -t code-lotgd .
```

Start the container:
```
docker run -p 0.0.0.0:80:80 -d -t --name code code-lotgd
```

To read the logs:
```
docker exec -t -i code cat /var/www/html/logs/code-lotgd.log
docker exec -t -i code cat /var/log/regenerate.log
```
