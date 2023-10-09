<?php

namespace App\Quests;

use App\Quiz\Checker\CheckQuiz;
use App\Quiz\Creator\CreateQuiz;
use App\Quiz\Domain\CheckedQuiz\CheckedQuestion;
use App\Quiz\Domain\CheckedQuiz\Quiz as CheckedQuiz;
use App\Quiz\Domain\NewQuiz\Quiz as NewQuiz;
use App\Quiz\Domain\QuestionPool\Question;
use App\Quiz\Domain\SolvedQuiz\AnsweredQuestion;
use App\Quiz\Domain\SolvedQuiz\AnswerOption;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsCommand(
    name: 'inquizitarium:dwarfs-kingdom:enter',
    description: 'Enter the Inquizitarium and face your destiny! ["The Dwarfs Kingdom" edition]',
)]
final class EnterDwarfsKingdomCommand extends Command
{
    private const DIFFICULTY_LEVELS = ['impossible', 'tough', 'normal'];

    private bool $debugMode = false;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly ?LoggerInterface $logger = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'difficulty',
            'd',
            InputOption::VALUE_OPTIONAL,
            sprintf(
                'Quest difficulty [%s]. The harder the difficulty - the more fun you get!',
                implode(', ', self::DIFFICULTY_LEVELS)
            ),
            'impossible',
            self::DIFFICULTY_LEVELS,
        );
        $this->addOption(
            name: 'waiting',
            mode: InputOption::VALUE_NEGATABLE,
            description: 'Enables waiting bars during the quest. Turn off [--no-waiting] if you get bored',
            default: true,
        );
    }

    // ######################################################################################################
    // ######################           !!! WARNING !!! SPOILERS BELOW !!!           #######################
    // ######################################################################################################

    /**
     * Be aware!!!
     *
     * It is not recommended to read the content of the execute method if you have not tried to run the command yet!
     *
     * Do try playing the quest without knowing the story first!!! It would give you way more fun! I promise! :)
     *
     * @psalm-suppress UnnecessaryVarAnnotation
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugMode = $output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
        $difficulty = (string) $input->getOption('difficulty');
        if (!in_array($difficulty, self::DIFFICULTY_LEVELS, true)) {
            throw new InvalidArgumentException('Invalid value for --difficulty option. Try --help for more details.');
        }
        $canWait = (bool) $input->getOption('waiting');
        $immersiveWaitingTime = $canWait ? 10 : 0; // subjective, defined experimentally (not too slow, not too fast)
        $io = new SymfonyStyle($input, $output);

        $io->caution([
            'You are about to enter the Inquizitarium - The Dwarfs Kingdom!',
            'There is a real chance that you might not survive ☠️  in this adventure... ',
        ]);

        if ("No way! I'm not a madman!" === $io->choice("Do you accept this challenge? [difficulty is '$difficulty']", [
            "No way! I'm not a madman!",
            "Hmm.. Ok let's do it!",
        ], default: 0)) {
            $io->section('Wise choice! Think it over and get back when become ready..');

            return Command::SUCCESS;
        }

        $io->title(\PHP_EOL.'!!! Welcome to Inquizitarium - The Dwarfs Kingdom !!!');
        $io->warning('From now on, your destiny totally depends on your actions! Be careful.. Good luck! 🍀');
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment([
            "Just after you get through the enter, you've noticed an unexpectedly huge! and scary Dwarf! 😱",
            "He didn't see you. Yet..",
        ]);
        $hasTriedToRunAway = 'Run away!' === $io->choice('What will you do? Think fast! 😱', [
            'Run away!',
            "I am brave! So I'm moving on!",
        ], default: 0);

        if ($hasTriedToRunAway) {
            $io->comment([
                "You've always been gutless.. ",
                'You have turned around to run away, but another Dwarf, way smaller, appeared out of nowhere!..',
            ]);
            $io->note([
                "Where do you think you're going? [he looked calm and patient]",
                'There is no way out. Follow me..',
            ]);
            $io->comment('You was too scared and had to obey..');
        } else {
            $io->comment("You've got the courage and came closer. The Dwarf has noticed you far enough and called you out..");
        }
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->note(['You! [the big dwarf yelled loudly]', 'Go!', 'Here!', 'I get a sack.', 'You wait!', 'AND NOT MOVE!']);
        $io->comment([
            "The big Dwarf's speech is clumsy, but his voice is harsh.",
            'Wait, what sack?!',
            'He stepped though the door behind him and disappeared..',
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        // creating a quiz is a potentially time-consuming operation that might fail in many ways

        try {
            if ('impossible' === $difficulty) {
                // using different difficulty helps to emulate failures but make them as part of the story
                throw new \RuntimeException('There is no way for you to survive in "impossible" mode.. Try something easier [--difficulty=tough]');
            }
            /** @var NewQuiz $createdQuiz */
            $createdQuiz = $this->executeCommand(new CreateQuiz());
            $this->debug($createdQuiz);
            $questions = $createdQuiz->getQuestions();
            $questionsCnt = count($questions);
        } catch (\Throwable $e) {
            $this->displayWaitingIndicator(
                $output,
                hint: "You are waiting, trying to stand still.. It's better to be patient and stay put.. Just wait and don't touch anything..",
                result: $hasTriedToRunAway
                    ? "Looking around you've noticed a way out without any guards! With no hesitation you ran towards it, but.."
                    : 'Boring! You could not wait any longer and without thinking followed the Dwarf, but..',
                seconds: $immersiveWaitingTime,
            );
            $this->askConfirmationToProceed($io, force: !$canWait);

            $io->comment([
                '.. just after the first step, a sharp sword 🗡  stabbed you in the heart 🫀  from behind.',
                "It was a flash ⚡️ - you couldn't even realize who did it 🥷",
            ]);
            $io->error('You died instantly. With no pain..');

            $this->logger?->critical($e->getMessage(), [
                'difficulty' => $difficulty,
                'exception' => $e,
            ]);

            $this->printHelp($io);

            return Command::FAILURE;
        }

        $this->displayWaitingIndicator(
            $output,
            hint: 'It\'s better to be patient and stay put.. Just wait..',
            result: $canWait ? 'He is back!' : 'Wow! That was fast!.. When did he get back?!',
            seconds: $immersiveWaitingTime,
        );
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->note(['You!', 'Here?', 'Good you.', 'Start time.']);
        $io->comment([
            'The Dwarf brought a brown dirty sack with lots of durable wooden cards.',
            "He took $questionsCnt out of the sack, put the sack aside, and handed the first one to you..",
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->note(['Go!', 'Take!', 'You!', $hasTriedToRunAway ? 'Coward..' : 'Silly..', 'Chicken 🐔']);
        $io->comment([
            "He doesn't look clever, but he is huge! Could carry a ton of cards.. The Big one 💪 !",
            "You've put together all the bravery you had and took the card..",
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->warning([
            "This is the main part! It's time to focus and be brilliant!!! 🤓",
            'You need to choose at least one option.',
            'Use UP and DOWN arrows to make your choice or type multiple options separated by comma (,)',
        ]);
        $io->comment('You turned the card around, and noticed additional but ambiguous instruction, what would that mean?..');
        $io->section('https://symfony.com/doc/current/components/console/helpers/questionhelper.html#multiple-choices');
        $io->comment("Whatever.. move on! What's on the first card?!");
        $this->askConfirmationToProceed($io, force: !$canWait);

        $answeredQuestions = [];
        foreach ($questions as $i => $question) {
            $answeredQuestions[] = $this->askQuizQuestion($question, $io);
            if (++$i === $questionsCnt) {
                break;
            }
            $progressComment = $questionsCnt - $i.' more left!';
            if (1 === $i) {
                $progressComment = [
                    'You handed the first card back to the Big one.',
                    'He took it and showed it to another dwarf. [the Smart one 🧠 ?]',
                    'He took a brief look and nodded approvingly.',
                    'The Big one put the card to another white sack which seemed empty, and handed the next card to you.. ',
                    'So, now you know the drills! Looks like you just need to go ahead and see what happens next?!.. ',
                    $progressComment,
                ];
            }
            $io->comment($progressComment);
        }
        $io->comment('It was the last card! You are done!');
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->text('The last one!? Take it'.(
            $hasTriedToRunAway
                ? ', please.. [your voice is soft but full of confidence]'
                : '.. [your voice is firm but full of doubts]'
        ));
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment('The Big one took the last card, put it into the white sack, and gave the sack to the Smart one.');
        $io->note("It won't take much.. [assured the Smart one]");
        $this->askConfirmationToProceed($io, force: !$canWait);

        // another potentially time-consuming command that might fail if something goes wrong...

        try {
            if ('tough' === $difficulty) {
                throw new \RuntimeException('Well.. you could try [--difficulty=normal] then..');
            }
            /** @psalm-var non-empty-list<AnsweredQuestion> $answeredQuestions */
            $command = new CheckQuiz($createdQuiz->getId(), $answeredQuestions);
            $this->debug($command);
            /** @var CheckedQuiz $checkedQuiz */
            $checkedQuiz = $this->executeCommand($command);
            $this->debug($checkedQuiz);
        } catch (\Throwable $e) {
            $io->comment([
                'Suddenly the Smart one has snapped his fingers and the ground beneath you immediately vanished..',
                '.. and now you are falling into an endless hole!!!',
            ]);
            $io->note('No more cards for silly chicken!.. 🤣🤣 [Both dwarfs laughed loudly]');
            $io->comment('Your scream echoed through the whole Kingdom until it disappeared in the endlessness..');
            $this->askConfirmationToProceed($io, force: !$canWait);

            $io->error(['You are tough! 😎', 'But not enough! 🤣', 'No happy ending here.. 👎']);

            $this->logger?->critical($e->getMessage(), [
                'difficulty' => $difficulty,
                'exception' => $e,
            ]);

            $this->printHelp($io);

            return Command::FAILURE;
        }

        if ($canWait) {
            $io->comment('He turned around and was gone somewhere around the nearest corner..');
            $io->note('You!');
            $io->comment('The Big one took your attention back');
            $io->note(['Chickens wait.', 'Noo moovee!']);
            if (!$hasTriedToRunAway) {
                $io->comment('No way! Again?!');
            }
            $this->askConfirmationToProceed($io, force: !$canWait);
        }

        $io->comment('Well.. Fine..');

        if ($canWait) {
            // a hidden quest here... for those who are patient :)
            $this->displayWaitingIndicator(
                $output,
                hint: 'You hate waiting! But you knew it the first hand - sometimes patience is the key to success!',
                result: 'What\'s that? You\'ve heard a noise beside you and looked around..',
                seconds: $immersiveWaitingTime,
            );
            $this->askConfirmationToProceed($io);
            $io->comment([
                'It is a rat!.. 🐀  got into an empty bucket 🪣   and being stuck now..',
                "You've been watching the rat trying to get out of the bucket, but it had no chance..",
                "The big Dwarf did not hear it and wasn't watching you while doing his own stuff.",
                'You could easily free the rat, but why the heck would you do that?',
                'After all.. you got a command to stay put.. remember?',
            ]);
            $hasSavedTheRat = 'Damn it! I should help' === $io->choice('What do you do? The Smart one might be back anytime!..', [
                "Screw it! I know how that works! I'm not moving! Not this time! Who cares about the freaking rat?!",
                'Damn it! I should help',
            ], default: 0);

            $hasSavedTheRat
                ? $io->comment([
                    $hasTriedToRunAway
                        ? 'You might be a coward, but you are not letting a living being to die!'
                        : "You're brave as hell! You are born for helping the weak! No other options..",
                    'You looked around making sure no one is watching and took a quick leap towards the bucket.',
                    'You laid the bucket on its side and hurried back 🏃  to take your place!',
                    'Wow! You are fast! Faster than wind 💨  You should be proud of yourself!',
                    'The nimble rat 🐭  has already found its way out - there was no sign of it when you looked around..',
                ])
                : $io->comment("You're not going to disobey to save that stupid rat.. You shouldn't move - you've learnt that!");
            $this->askConfirmationToProceed($io);
        }

        $this->displayWaitingIndicator(
            $output,
            hint: 'Just stay calm and be patient..',
            result: $canWait
                ? 'He is coming back!'
                : 'He has shaken the sack up, then pulled one card from it and took a thorough look..',
            seconds: $immersiveWaitingTime,
        );
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment('The Smart one came closer and threw the sack at your feet.');
        $io->note([
            $checkedQuiz->isPassed() ? 'SUCCESS!' : 'FAILED!',
            'You may have a look..',
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $groups = $this->splitCheckedQuestionsIntoTwoGroups($checkedQuiz->getQuestions());
        $this->debug($groups);
        $hasTwoNonEmptyLists = !empty($groups['correct']) && !empty($groups['incorrect']);
        $io->comment([
            "You didn't know what to expect but curiosity got the better 🤔",
            'You laid the cards out on the ground'
                .($hasTwoNonEmptyLists ? ', split them into two groups' : '')
                .' and squinted trying to review them carefully.. 🧐',
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        foreach ($groups as $index => $questionGroup) {
            if ($hasTwoNonEmptyLists) {
                'correct' === $index
                    ? $io->comment('The first group had cards with no mistakes!')
                    : $io->comment('The other one, well, you could do better..');
            }
            foreach ($questionGroup as $q) {
                $definitionList = $this->convertQuestionToDefinitionList($q);
                $this->debug($definitionList);
                $io->definitionList(...$definitionList);
            }
        }

        if (!$checkedQuiz->isPassed()) {
            $io->comment([
                'Crap! You should have done it better!',
                "You have failed the test - it couldn't mean any good to you...",
                'So you got ready to '.($hasTriedToRunAway ? 'run as fast as you can' : 'fight to the death').', but..',
            ]);
        } else {
            $io->comment('No way! Just look at you! You nailed it!');
        }
        $this->askConfirmationToProceed($io, force: !$canWait);

        if (isset($hasSavedTheRat) && true === $hasSavedTheRat) {
            $io->note('Now you are allowed to pass..');
            $this->askConfirmationToProceed($io);

            if (!$checkedQuiz->isPassed()) {
                $io->text('Wait, what did you just say?! [surprised]');
                $io->comment('How could that be? You have failed, but they are allowing you to go further?');
            } else {
                $io->text('Really? That easy?! [surprised]');
                $io->comment([
                    'You was happy, but could not believe it and was expecting..',
                    '.. what else could happen here?..',
                ]);
            }
            $this->askConfirmationToProceed($io);

            $io->comment('The smart Dwarf pulled an old scroll out from a pocket, opened it, and gave it to you..');
            $this->askConfirmationToProceed($io, force: !$canWait);

            $io->section('"There is now place in the Kingdom for those not being able to follow the rules!"');
            $io->section('"For those having patience and helping others - the Kingdom is always open.."');
            $this->askConfirmationToProceed($io, force: !$canWait);

            $io->comment('You have finished reading the scroll, and noticed a restrained smile on his face..');
            $this->askConfirmationToProceed($io, force: !$canWait);

            $io->success([
                'The Dwarf led you the way into the Kingdom!',
                'You followed the way being excited about the upcoming adventures!',
                'But that is another story to tell...',
            ]);

            $io->section('Congratulations! You passed the guards of the Dwarfs Kingdom!');
            $io->section('Thanks for playing!');

            return Command::SUCCESS;
        }

        $checkedQuiz->isPassed()
            ? $io->note('You can get your reward..')
            : $io->note(['Calm down..', 'You deserve a reward anyway..']);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->text('A reward?! 🤨');
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment('The Big one has started rummaging in his pockets. He pulled out a small bundle and handed to you.');
        $io->note(['For chicken!', 'Cheese.. 🧀']);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment('Cheese? Really? You must be kidding! Is it the Reward?');
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment("On the other hand, it's been quite a while since you came here.. with an empty stomach..");
        $hasTakenTheCheese = 'Gimme that!' === $io->choice('Do you take the reward?', [
                $hasTriedToRunAway
                    ? "Thank you! But I'm not hung.."
                    : 'Is it a joke?!?! Keep this "reward" for yourself! You f..',
                'Gimme that!',
            ], default: 0);

        if ($hasTakenTheCheese) {
            $io->comment([
                'You took the bundle, unfasten it, and found a piece of fresh good smelling cheese!',
                'Indeed, that was cheese, the dwarf did not lie.',
            ]);
            $io->text("Well, it's better than nothing, right? And smells good! [trying to joke]");
            $io->comment([
                'The dwarfs showed no signs of fun.',
                'Saying no more you ate the whole piece at once!',
                'The Smart one handed a cup of water to you..',
            ]);
            $this->askConfirmationToProceed($io, force: !$canWait);
            $io->text('Thamks! [chewing the cheese]');
            $io->comment([
                'The cheese was good! And the water came in handy..',
                'But just after you finished, you felt something is wrong.',
                'Then everything went dark and you instantly fell down unconscious.. 😵 💫',
                'What was that? A poison? 🤢',
            ]);
        } else {
            $io->comment([
                "You didn't have time to finish..",
                'In a moment the Big one threw the bundle to the Smart one.. but that was just to divert your attention! 👀',
                "In the next moment you've got a heavy punch 👊 in your face from the Big one!!! 💪",
                "You couldn't resist such a strength and instantly fell down unconscious.. 😵 💫",
            ]);
        }
        $this->askConfirmationToProceed($io, force: !$canWait);

        $this->displayWaitingIndicator($output, '', 'whhh.. whereami?', seconds: $immersiveWaitingTime);
        $this->displayWaitingIndicator(
            $output,
            hint: 'You feel dizzy, trying to get over it..',
            result: 'Wwhat?.. What happened?',
            seconds: $immersiveWaitingTime,
        );
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->text($hasTakenTheCheese ? 'Dirty trick! 🤮' : 'That is painful! 🤕');
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment('You awoke outside.. in front of the entrance into the Dwarfs Kingdom!');
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->text([
            'Those.. 🤬   bastards!',
            "They've just kicked me out of there!",
            'It is unfair!!!',
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment([
            'You tried so hard and got so far! 🎵',
            "But in the end, it doesn't even matter.. 🎵",
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->comment([
            'You felt devastated.. 😞',
            'But the gut keeps telling you - there is still something you have missed in there!',
        ]);
        $this->askConfirmationToProceed($io, force: !$canWait);

        $io->error([
            'YOU!',
            'HAVE!',
            'FAILED!',
            '...',
            'Again! 😈',
            "You did survive this time though! 👌  I'll give you that.. ",
            'Maybe you will be luckier 🍀  next time!?',
            'If you dare..',
        ]);
        $io->info([
            'Seek for --help if you got stuck!',
            'Use --no-waiting to skip annoying delays if you are impatient or short of time.',
        ]);

        return Command::SUCCESS;
    }

    private function printHelp(SymfonyStyle $io): void
    {
        $io->info([
            'Seek for --help if you got stuck!',
            'Use --no-waiting to skip annoying delays if you are short of time or impatient.',
        ]);
    }

    /**
     * @psalm-param non-empty-list<CheckedQuestion> $questions
     *
     * @psalm-return array<string, list<CheckedQuestion>>
     */
    private function splitCheckedQuestionsIntoTwoGroups(array $questions): array
    {
        return array_reduce($questions, static function (array $groups, CheckedQuestion $question) {
            $groupIndex = $question->isAnswerCorrect() ? 'correct' : 'incorrect';
            $groups[$groupIndex][] = $question;

            return $groups;
        }, ['correct' => [], 'incorrect' => []]);
    }

    private function debug(mixed ...$vars): void
    {
        if ($this->debugMode) {
            /* @noinspection ForgottenDebugOutputInspection */
            dump(...$vars);
        }
    }

    /**
     * TODO: why psalm does not recognize the return type properly?
     *
     * @template TCommand of object
     *
     * @psalm-param TCommand $command
     *
     * @psalm-return ($command is CreateQuiz ? NewQuiz : ($command is Checkquiz ? CheckedQuiz : null))
     */
    private function executeCommand(object $command): mixed
    {
        return $this->commandBus->dispatch($command)->last(HandledStamp::class)?->getResult();
    }

    private function askQuizQuestion(Question $q, SymfonyStyle $io): AnsweredQuestion
    {
        $options = [];
        foreach ($q->getAnswerOptions() as $i => $option) {
            $options[sprintf('#%u', $i + 1)] = $option;
        }
        $this->debug($options);
        $selected = $io->choice($this->formatQuestion($q), $options, multiSelect: true);
        $this->debug($selected);

        $answers = [];
        foreach ($options as $i => $option) {
            $answers[] = new AnswerOption($option, isSelected: in_array($i, $selected, strict: true));
        }
        $this->debug($answers);

        return new AnsweredQuestion($q->getExpression(), $q->getComparisonOperator(), $answers);
    }

    private function askConfirmationToProceed(SymfonyStyle $io, bool $force = false): void
    {
        if (!$force) {
            $io->askHidden('[press "ENTER" to proceed]');
            $io->newLine();
        }
    }

    /**
     * I like it!
     *
     * @see https://symfony.com/doc/current/components/console/helpers/progressindicator.html#custom-indicator-values.
     *
     * @param non-negative-int $seconds
     */
    private function displayWaitingIndicator(OutputInterface $output, string $hint, string $result, int $seconds = 5): void
    {
        $pi = new ProgressIndicator($output, 'normal', indicatorValues: ['⠏', '⠛', '⠹', '⢸', '⣰', '⣤', '⣆', '⡇']);
        $pi->start($hint);
        while ($seconds-- > 0) {
            $pi->advance();
            sleep(1);
        }
        $pi->finish($result);
        $output->write(\PHP_EOL);
    }

    private function formatQuestion(Question|CheckedQuestion $q): string
    {
        return sprintf('%s %s ?', (string) $q->getExpression(), $q->getComparisonOperator()->value);
    }

    /**
     * Handy way to render lists in console.
     *
     * https://symfony.com/blog/new-in-symfony-4-4-horizontal-tables-and-definition-lists-in-console-commands
     */
    private function convertQuestionToDefinitionList(CheckedQuestion $q): array
    {
        $rows = [];
        $rows[] = [$this->formatQuestion($q) => ''];
        $rows[] = new TableSeparator();
        foreach ($q->getAnswers() as $a) {
            $expr = trim((string) $a->getExpression());
            $expr .= str_repeat(' ', times: 15 - strlen($expr));
            $result = match ($a->isCorrect()) {
                true => '✅',
                false => '❌',
                null => '',
            };
            $rows[] = [$expr => $result];
        }

        return $rows;
    }
}
