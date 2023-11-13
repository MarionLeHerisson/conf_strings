<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;
use Symfony\Component\String\CodePointString;
use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\b; // ByteString
use function Symfony\Component\String\u; // UnicodeString
use function Symfony\Component\String\s; // Byte or Unicode
class HomeController
{
    #[Route('/', name: 'home')]
    public function homeAction(): void
    {

        $byteString1 = new ByteString("\xfe\xff");
        $byteString2 = (new ByteString("\xfe"));
        $byteString3 = new ByteString("\xff");
        $byteString3 = (new ByteString("\u0930\u0020\u042e"))->toCodePointString('UTF-16');

        dump(new UnicodeString('🤦🏼‍♂️'));

        // Example to go from a Unicode string to a byte string
//        $codePoint = 0x203d;
//        $glyph = '‽';
//        dump(UnicodeString::fromCodePoints($codePoint));
//        dump(dechex(mb_ord($glyph)));

        dump(UnicodeString::fromCodePoints(0x2621));
        dump(UnicodeString::fromCodePoints(0x2622));
        dump(UnicodeString::fromCodePoints(0x2623));
        dump(UnicodeString::fromCodePoints(0x2624));
        dump(UnicodeString::fromCodePoints(0x2625));

        // Generates a random string of 12 bytes
        $foo = ByteString::fromRandom(12)->toCodePointString();
        dump($foo);
        $foo = (new CodePointString('hello'))->toUnicodeString();
        dump($foo);
        $foo = UnicodeString::fromCodePoints(0x68, 0x65, 0x6C, 0x6C, 0x6F)->toByteString();
        $foo = UnicodeString::fromCodePoints(0x203d, 0x65, 0x6C, 0x6C, 0x6F)->toByteString();
        dump($foo);

// the optional $toEncoding argument defines the encoding of the target string
        $foo = (new CodePointString('hello'))->toByteString('Windows-1252');
        dump($foo);
// the optional $fromEncoding argument defines the encoding of the original string
        $foo = (new ByteString('さよなら'))->toCodePointString();
        dump($foo);

        // one code point : U+00E5
        $normalizedString = u('å')->normalize(UnicodeString::NFC);
        dump($normalizedString->codePointsAt(0)); // [229]

        // two code points : U+0061 U+030A
        $normalizedString = u('å')->normalize(UnicodeString::NFD);
        dump($normalizedString->codePointsAt(0)); // [97, 778]


die;
//        dump($cheeseWedge);
//        dd($byteString1, $byteString2, $byteString3, $codePointString, $unicodeString);


    }
}