<template>
    <div class="achievements" :class="gridColumns">
        <template v-if="hasAchievement">
            <div
                v-for="achievement of achievements"
                :key="achievement.type"
                class="achievement"
                @mouseover.self="onHover($event, achievement.title, achievement.hover_text)"
                @mouseout.self="onLeaved()"
            >
                <font-awesome-icon
                    class="achievement-icon"
                    :class="achievement.color"
                    :icon="achievement.icon"
                ></font-awesome-icon>
                <span v-if="achievement.extra_text !== undefined" class="extra-text">
                    {{ achievement.extra_text }}
                </span>
            </div>
            <div v-show="hoverWindow.show" class="hover-box"
                 :style="{ bottom: hoverWindow.bottom+'px', left: hoverWindow.left+'px'}">
                <div class="hover-title">{{ hoverWindow.title }}</div>
                <div v-show="hoverWindow.text !== undefined && hoverWindow.text !== ''" class="hover-text">
                    {{ hoverWindow.text }}
                </div>
            </div>
        </template>
        <div v-else class="no-achievement">
            実績なし
        </div>
    </div>

</template>

<script lang="ts">
import {defineComponent, PropType} from 'vue'
import {library} from "@fortawesome/fontawesome-svg-core";
import {faCrown, faDragon, faGem, faMedal, faQuestionCircle, faSkull} from "@fortawesome/free-solid-svg-icons";
import {faMountainCity} from "@fortawesome/free-solid-svg-icons";
import {
    Achievement,
    AchievementProp,
    filterDuplicatedAchievementType,
    getAchievementsList, sortAchievements
} from "../store/Entity/Achievement";

export default defineComponent({
    data() {
        return {
            hoverWindow: {
                bottom: 100,
                left: 100,
                title: "",
                text: "",
                show: false,
            },
            BOTTOM_OFFSET: 40
        }
    },
    setup(props) {
        library.add(faMedal, faMountainCity, faSkull, faDragon, faGem, faQuestionCircle, faCrown);

        let achievements: Achievement[];
        if (props.achievement_data === undefined) {
            achievements = filterDuplicatedAchievementType(getAchievementsList(props.achievement_props));
        } else {
            achievements = filterDuplicatedAchievementType(props.achievement_data);
        }

        sortAchievements(achievements);

        let cols = props.max_cols;
        if (achievements.length < cols)  cols = achievements.length;

        return {achievements, cols};
    },
    computed: {
        gridColumns() {
            return "grid-cols-" + this.cols;
        },
        hasAchievement() {
            return this.achievements !== null && this.achievements !== undefined && this.achievements.length > 0;
        }
    },
    methods: {
        onHover(event: MouseEvent, title: string, text: string) {
            const target = event.target as HTMLElement;

            this.hoverWindow.show = true;
            this.hoverWindow.title = title;
            this.hoverWindow.text = text;

            this.hoverWindow.left = target.offsetLeft + (target.offsetWidth / 2);
            this.hoverWindow.bottom = this.BOTTOM_OFFSET;
        },
        onLeaved() {
            this.hoverWindow.show = false
        }
    },
    props: {
        achievement_data: {
            require: false,
            type: Array as PropType<Achievement[]>
        },
        achievement_props: {
            require: false,
            type: Array as PropType<AchievementProp[]>
        },
        max_cols: {
            require: false,
            type: Number,
            default: 5
        }
    }
})
</script>

<style scoped lang="scss">
.achievements {
    @apply relative w-full grid;

    .achievement {
        @apply flex flex-wrap justify-center leading-none my-1;

        .achievement-icon {
            @apply block pointer-events-none;

            &.normal {
                @apply text-achievement-normal;
            }

            &.bronze {
                @apply text-achievement-bronze;
            }

            &.silver {
                @apply text-achievement-silver;
            }

            &.gold {
                @apply text-achievement-gold;
            }
        }

        .extra-text {
            @apply block w-full text-center text-[0.6rem];
        }
    }

    .hover-box {
        @apply absolute w-[120px] h-fit z-30 pointer-events-none;
        @apply bg-surface-variant text-sm leading-none text-on-surface-variant px-1 py-2 border rounded-md border-on-surface-variant drop-shadow-md;
        @apply -translate-x-1/2;

        &:before {
            @apply absolute left-[calc(50%-8px)] right-[calc(50%-8px)] -bottom-[12px] h-3 bg-on-surface-variant;
            content: "";
            clip-path: polygon(0 0, 50% 100%, 100% 0);
        }

        &:after {
            @apply absolute left-[calc(50%-8px)] right-[calc(50%-8px)] -bottom-[11px] h-3 bg-surface-variant;
            content: "";
            clip-path: polygon(0 0, 50% 100%, 100% 0);
        }

        .hover-title {
            @apply text-xs leading-none text-center;
        }

        .hover-text {
            @apply text-xs leading-none mt-1 border-t border-on-surface-variant
        }
    }
}

.no-achievement {
    @apply w-full text-center text-sm text-on-surface-variant;
}
</style>
