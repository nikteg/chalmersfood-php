<?php

require __DIR__ . '/vendor/autoload.php';

use function Krak\Fn \{
  head,
  filter,
  filterKeys,
  toArray,
  map,
  flatMap
};
use PHPHtmlParser\Dom;
use Carbon\Carbon;

function carboncloudURL(string $id)
{
  return "https://carbonateapiprod.azurewebsites.net/api/v1/mealprovidingunits/${id}/dishoccurrences";
}

function carboncloudAppendDates(string $url)
{
  $startDay = idate("d") - idate("w") + 1;

  $beginningDateString = date("Y-m-d", mktime(0, 0, 0, date("n"), $startDay));
  $endDateString = date("Y-m-d", mktime(0, 0, 0, date("n"), $startDay + 4));

  return "${url}?startDate=${beginningDateString}&endDate=${endDateString}";
}

function carboncloudRecipeCategory($item)
{
  $swedishDisplayName = head(filter(function ($displayName) {
    return $displayName->displayNameCategory->displayNameCategoryName == "Swedish";
  }, $item->displayNames))->dishDisplayName;

  if ($item->dishType) {
    $uppercaseCategory = mb_strtoupper($item->dishType->dishTypeName);
    return "${uppercaseCategory} – ${swedishDisplayName}";
  } else {
    return $swedishDisplayName;
  }
}

function carboncloudMapper($items)
{
  $grouped = [];
  foreach ($items as $item) {
    $grouped[$item->startDate][] = $item;
  }

  ksort($grouped);

  $weekWithNames = map(function ($week) {
    $names = toArray(map(carboncloudRecipeCategory, $week));

    sort($names);

    return $names;
  }, $grouped);

  return toArray($weekWithNames);
}

$restaurants = [
  [
    "name" => "Kårresturangen",
    "fetcher" => function () {
      return json_decode(file_get_contents(carboncloudAppendDates(carboncloudURL("21f31565-5c2b-4b47-d2a1-08d558129279"))));
    },
    "mapper" => carboncloudMapper,
  ],
  [
    "name" => "Linsen",
    "fetcher" => function () {
      return json_decode(file_get_contents(carboncloudAppendDates(carboncloudURL("b672efaf-032a-4bb8-d2a5-08d558129279"))));
    },
    "mapper" => carboncloudMapper,
  ],
  [
    "name" => "Express",
    "fetcher" => function () {
      return json_decode(file_get_contents(carboncloudAppendDates(carboncloudURL("3d519481-1667-4cad-d2a3-08d558129279"))));
    },
    "mapper" => carboncloudMapper,
  ],
  [
    "name" => "S.M.A.K.",
    "fetcher" => function () {
      return json_decode(file_get_contents(carboncloudAppendDates(carboncloudURL("3ac68e11-bcee-425e-d2a8-08d558129279"))));
    },
    "mapper" => carboncloudMapper,
  ],
  [
    "name" => "Einstein",
    "fetcher" => function () {
      return file_get_contents("http://restaurang-einstein.se/");
    },
    "mapper" => function ($html) {
      $dom = new Dom;
      $dom->load($html);

      $content = $dom->find("#column_gnxhsuatx .content-wrapper .column-content");

      $filtered = filterKeys(function ($index) {
        return $index % 2 == 1;
      }, $content);

      $mapped = map(function ($elem) {
        $paragrahps = $elem->find("p");

        if (empty(toArray($paragrahps))) {
          return [];
        }

        // var_dump(toArray($paragrahps));

        $flatMapped = flatMap(function ($paragraph) {
          return $paragraph->getChildren();
        }, $paragrahps);

        // var_dump(toArray($flatMapped));

        $filtered2 = filter(
          function ($node) {
            return $node->isTextNode();
          },
          $flatMapped
        );

        $mapped2 = map(function ($node) {
          return trim($node->text);
        }, $filtered2);

        return toArray($mapped2);
      }, $filtered);

      return toArray($mapped);
    }
  ]
];

class Cache
{
  public $items = [];
  public $timestamp = 0;

  function __construct($items = [], $timestamp = 0)
  {
    $this->items = $items;
    $this->timestamp = $timestamp;
  }
}

function readCache($filename): Cache
{
  if (!file_exists($filename)) {
    return new Cache();
  }

  $contents = file_get_contents($filename);

  if ($contents === false) {
    return new Cache();
  }

  $cache = unserialize($contents);

  if (empty($cache->items)) {
    return new Cache();
  }

  return $cache;
}

function writeCache(Cache $cache, $filename)
{
  return file_put_contents($filename, serialize($cache));
}

function removeCache($filename) {
  if (file_exists($filename)) {
    unlink($filename);
  }
}

function fetch($restaurant)
{
  $fetcher = $restaurant["fetcher"];
  $mapper = $restaurant["mapper"];

  return $mapper($fetcher());
}

function menuItemClasses(int $index, int $selectedDay, int $today)
{
  $classes = [];

  if ($index === $selectedDay) {
    $classes[] = "active";
  }

  if ($index === $today) {
    $classes[] = "today";
  }

  return join(" ", $classes);
}

$cachePath = __DIR__ . "/cache";

if (isset($_GET["refresh"])) {
  removeCache($cachePath);
  header("Location: /");
  exit;
}

$cache = readCache($cachePath);

if (time() > ($cache->timestamp + 1 * 60 * 30)) {
  $items = [];

  foreach ($restaurants as $r) {
    $name = $r["name"];
    $items[$name] = fetch($r);
  }

  $cache = new Cache($items, time());
  writeCache($cache, $cachePath);
}

$today = idate("w") - 1;
$selectedDay = isset($_GET["day"]) ? intval($_GET["day"]) : $today;
$selectedDay = min(4, max(0, $selectedDay));

?>
<!DOCTYPE html>
<html>

<head>
    <title>Lunch på Chalmers</title>
    <link href="style.css" rel="stylesheet">
    <link href="favicon.png" rel="icon" type="image/png">
    <meta name="viewport" content="width=device-width">
</head>

<body>
    <header>
        <h1><a href="/">Lunch v. <?php echo date("W") ?></a></h1>
        <ul>
            <?php foreach (["Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag"] as $index => $day) : ?>
            <li class="<?php echo menuItemClasses($index, $selectedDay, $today) ?>">
                <a href="?day=<?php echo $index ?>"><?php echo $day ?></a>
            </li>
            <?php endforeach ?>
        </ul>
    </header>
    <div id="content">
        <ul>
            <?php foreach ($restaurants as $r) : ?>
            <li class="restaurant">
                <h3><?php echo $r["name"] ?></h3>
                <ul>
                    <?php foreach ($cache->items[$r["name"]][$selectedDay] as $item) : ?>
                    <li><?php echo $item ?></li>
                    <?php endforeach ?>
                </ul>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
    <footer>
        <div>Uppdaterad <?php echo Carbon::createFromTimestamp($cache->timestamp)->locale('sv')->diffForHumans() ?>...</div>
        <div><a href="/?refresh">Tvinga omladdning av menyer</a>&nbsp;|&nbsp;<a href="https://github.com/nikteg/chalmersfood-php">Källkod</a></div>
    </footer>
</body>

</html> 