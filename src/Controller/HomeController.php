<?php

namespace App\Controller;

use App\Form\StringType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Transliterator\EmojiTransliterator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\String\CodePointString;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\b; // ByteString
use function Symfony\Component\String\u; // UnicodeString
use function Symfony\Component\String\s; // Byte or Unicode

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function homeAction(): void
    {

        $byteString1 = new ByteString("\xfe\xff");
        $byteString2 = (new ByteString("\xfe"));
        $byteString3 = new ByteString("\xff");
        $byteString3 = (new ByteString("\u0930\u0020\u042e"))->toCodePointString('UTF-16');
dump($byteString1, $byteString2, $byteString3);
        // facepalm
//        dump(new UnicodeString('🤦🏼‍♂️'));

        // Example to go from a Unicode string to a byte string
//        $codePoint = 0x203d;
//        $glyph = '‽';
//        dump(UnicodeString::fromCodePoints($codePoint)); // ‽
//        dump(dechex(mb_ord($glyph))); // 203d

        dump(UnicodeString::fromCodePoints(0x00BC)); // ¼
        // Canonical composition (NFC)
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFC)->codePointsAt(0)); // 188
        dump(UnicodeString::fromCodePoints(dechex(0x0188))); // ¼
        // Canonical decomposition (NFD)
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFD)->codePointsAt(0)); // 188
        // Compatibility composition (NFKC)
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFKC)->codePointsAt(0)); // 49
        dump(UnicodeString::fromCodePoints(dechex(0x0049))); // 1
        dump(UnicodeString::fromCodePoints(0x0031)); // 1
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFKC)->codePointsAt(1)); // 8260
        dump(UnicodeString::fromCodePoints(dechex(0x8260))); // ⁄
        dump(UnicodeString::fromCodePoints(0x002F)); // /
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFKC)->codePointsAt(2)); // 52
        dump(UnicodeString::fromCodePoints(dechex(0x0052))); // 4
        dump(UnicodeString::fromCodePoints(0x0034)); // 4
        // Compatibility decomposition (NFKD)
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFKD)->codePointsAt(0)); // 49
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFKD)->codePointsAt(1)); // 8260
        dump(UnicodeString::fromCodePoints(0x00BC)->normalize(UnicodeString::NFKD)->codePointsAt(2)); // 52

        // Generates a random string of 12 bytes
        // ByteString::fromRandom(12)->toCodePointString();

// the optional $toEncoding argument defines the encoding of the target string
        $foo = (new CodePointString('hello'))->toByteString('Windows-1252');
        dump($foo);
// the optional $fromEncoding argument defines the encoding of the original string
        $foo = (new ByteString('さよなら'))->toCodePointString();
        dump($foo);

        // one code point : U+00E5
        $normalizedString = u('å')->normalize(UnicodeString::NFKD);
        dump($normalizedString);
        dump($normalizedString->codePointsAt(0)); // [229]

        // two code points : U+0061 U+030A
        $normalizedString = u('å')->normalize(UnicodeString::NFD);
        dump($normalizedString->codePointsAt(0)); // [97, 778]

        die;
    }

    #[Route('/norm', name: 'norm')]
    public function norm(): void
    {
        $ARing = "\xC3\x85"; // Å (U+00C5)
        $ARingComposed = "A"."\xCC\x8A";  // A◌̊ (U+030A)

        $norm1 = \Normalizer::normalize(
            $ARing, \Normalizer::FORM_C
        );
        $norm2 = \Normalizer::normalize(
            $ARingComposed, \Normalizer::FORM_C
        );

        var_dump($ARing === $ARingComposed); // FALSE
        var_dump($norm1 === $norm2); // TRUE

        dump(b($ARing)->toCodePointString()->codePointsAt(0));  // 197
        dump(b($ARingComposed)->toCodePointString()->codePointsAt(0)); // 65
        dump(b($ARingComposed)->toCodePointString()->codePointsAt(1)); // 778

        dump(u($ARing)->toCodePointString()->codePointsAt(0)); // 197
        dump(u($ARingComposed)->toCodePointString()->codePointsAt(0)); // 197 !!

        dump(s($ARingComposed)->toCodePointString()->codePointsAt(0)); // 197 too
        die;
    }

    #[Route('/emojis', name: 'emojis')]
    public function emojis(): void
    {
        dump(u('नमस्ते')->codePointsAt(0)); // [2344]
        dump(u('नमस्ते')->codePointsAt(1)); // [2360]
        dump(u('नमस्ते')->codePointsAt(2));
        dump(u('नमस्ते')->codePointsAt(3));

        $slugger = new AsciiSlugger();

        $slugger = $slugger->withEmoji();
        dump($slugger->slug('😺'));

        $slugger = $slugger->withEmoji('en');
        dump($slugger->slug('😺'));

        $slugger = $slugger->withEmoji('uk');
        dump($slugger->slug('😺'));

        $slugger = $slugger->withEmoji('github');
        dump($slugger->slug('😺'));

        $slugger = $slugger->withEmoji('strip');
        dump($slugger->slug('😺'));

        $slugger = $slugger->withEmoji('fr');
        dump($slugger->slug('😺 😺', '/', 'en')); // 'en' won't override the 'fr' setting

        $slugger = $slugger->withEmoji();
        dump($slugger->slug('😺 😺', 'TOTO', 'en')); // works here since there is no default setting
        die;
    }

    #[Route('/transliterator', name: 'transliterator')]
    public function transliterator(): void
    {
        dump(\transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', "A æ Übérmensch på høyeste nivå! И я люблю PHP! ﬁ"));
        dump(\transliterator_transliterate('Any-Latin; Lower()', "A æ Übérmensch på høyeste nivå! И я люблю PHP! ﬁ"));
// string(41) "a ae ubermensch pa hoyeste niva! i a lublu php! fi"

        $transliterator = EmojiTransliterator::create('github');
        $ghString = $transliterator->transliterate('Menus with 🥗 or 🧆');
        dump($ghString);

        $reverseTransliterator = EmojiTransliterator::create('github', EmojiTransliterator::REVERSE);
        $original = $reverseTransliterator->transliterate($ghString);
        dump($original);
        die;
    }

    #[Route('/turkish_i', name: 'turkish_i')]
    public function turkishI(): void
    {
        dump(dechex(mb_ord('i'))); // 69
        dump(dechex(mb_ord('ı'))); // 131
        dump(dechex(mb_ord('I'))); // 49
        dump(dechex(mb_ord('İ'))); // 130

        dump(u('i')->upper()->toString()); // I
        dump(u('ı')->upper()->toString()); // I
        dump(u('I')->lower()->toString()); // i
        dump(u('İ')->lower()->toString()); // i
        dump(mb_strtolower('İ')); // i̇

        dump(u('i')->upper()->codePointsAt(0)); // [73]
        dump(dechex(73));
        dump(u('ı')->upper()->codePointsAt(0)); // [73]
        dump(dechex(73));
        dump(u('I')->lower()->codePointsAt(0)); // [105]
        dump(dechex(105));
        dump(u('İ')->lower()->codePointsAt(0)); // [105, 775] WTF ?!
        dump(u('İ')->codePointsAt(0)); // 304

        dump(u('i̇'));

        die;
    }

    #[Route('/cmp', name: 'cmp')]
    public function cmp(Request $request): Response
    {
        dump(u('👩‍👩‍👧‍👦')->length()); // 1

        dump(u('🤦🏼‍♂️')->codePointsAt(0));
        dump((new ByteString('👩‍👩‍👧‍👦'))->length());
        dump((new CodePointString('👩‍👩‍👧‍👦'))->length());
        dump((new UnicodeString('👩‍👩‍👧‍👦'))->length());
        dump('👩‍👩‍👧‍👦');
        dump(UnicodeString::fromCodePoints(0x1F469, 0x0200D, 0x1F469, 0x0200D, 0x1F467, 0x0200D, 0x1F466));

        $foo1 = dechex(b("Å")->toCodePointString()->codePointsAt(0)[0]);
        $foo2 = dechex(b("Å")->toCodePointString()->codePointsAt(1)[0]);
        $bar = dechex(b("Å")->toCodePointString()->codePointsAt(0)[0]);
        $baz = dechex(b("Å")->toCodePointString()->codePointsAt(0)[0]);

        $unifoo = dechex(u("Å")->codePointsAt(0)[0]);
        $unibar = dechex(u("Å")->codePointsAt(0)[0]);
        $unibaz = dechex(u("Å")->codePointsAt(0)[0]);

        dump($foo1, $foo2, $bar, $baz);
        dump($unifoo, $unibar, $unibaz);
        die;
    }

    #[Route('/sutton', name: 'sutton')]
    public function sutton()
    {
        $mySutton = UnicodeString::fromCodePoints(121343, 121451, 121434, 121360)->toString();
        $mySutton2 = UnicodeString::fromCodePoints(121343, 121369, 121346, 121392)->toString();
        dump($mySutton, $mySutton2);

        die;
    }
}

interface 🍚 {}
interface 🐟 {}

class 🍣 implements 🍚, 🐟 {

}