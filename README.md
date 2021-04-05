## Sculping Contentful Bundle

Connecting Contentful 

### How to use

`composer require airtonzanon/sculpin-contentful-bundle`

On `app/SculpinKernel.php` you should add:

```php
    // ...
    protected function getAdditionalSculpinBundles(): array
    {
        return [
            SculpinContentfulBundle::class,
        ];
    }
    // ...
```

```bash
# Environment variables
$ export contentful_token=<token>
$ export contentful_space_id=<space_id>

# Sculpin command
$ vendor/bin/sculpin contentful:fetch
Created file: source/_til/2021-04-05-first-post.md
Created file: source/_til/2020-12-05-second-post.md
Created file: source/_til/2020-11-23-third-post.md
```


